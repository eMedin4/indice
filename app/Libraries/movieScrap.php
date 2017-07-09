<?php
namespace App\Libraries;

use App\Libraries\Format;

class MovieScrap
{

	private $format;

	public function __Construct(Format $format)
	{
		$this->format = $format;
	}

	public function getMovie($crawler) 
	{

		/*
	    |--------------------------------------------------------------------------
	    | FILMAFFINITY
	    |--------------------------------------------------------------------------
	    */

		/* Filmaffinity ID */
		if ($crawler->filter('.ntabs a')->count())
			$data['Fa Id'] = $this->format->faId($crawler->filter('.ntabs a')->eq(0)->attr('href'));
		else return ['error' => 'No se encuentra ID de filmaffinity en la clase .ntabs a'];
		
		/* TITULO */
		$data['Fa Title'] = $crawler->filter('#main-title span')->text();
		$data['Fa Title'] = $this->format->removeString($data['Fa Title'], '(TV)');

		/* PUNTUACIONES FILMAFFINITY */
		if ($this->format->getElementIfExist($crawler, '#movie-rat-avg', NULL) && $this->format->getElementIfExist($crawler, '#movie-count-rat span', NULL)) {
			$data['Fa Rating'] = $this->format->float($crawler->filter('#movie-rat-avg')->text());
			$data['Fa Count'] = $this->format->integer($crawler->filter('#movie-count-rat span')->text());
		} else {
			$data['Fa Rating'] = $data['Fa Count'] = NULL;
		}

		//Construimos array con los datos de la table(no tienen ids)
        $table = $crawler->filter('.movie-info dt')->each(function($element) {
            return [$element->text() => $element->nextAll()->text()];
        });
        //Devuelve un array de arrays, lo convertimos a array normal
        foreach ($table as $key => $value) { 
            $table2[key($value)] = current($value);
        }

        /* DATOS DE LA TABLA DE FA */
		$data['Fa Year'] = $this->format->getValueIfExist($table2, 'Año');
		$data['Fa Original'] = $this->format->cleanData($this->format->getValueIfExist($table2, 'Título original'));
		$data['Fa Country'] = $this->format->cleanData($this->format->getValueIfExist($table2, 'País'));
		$data['Fa Duration'] = $this->format->integer($this->format->getValueIfExist($table2, 'Duración'));
		$data['Fa Review'] = $this->format->removeString($this->format->getValueIfExist($table2, 'Sinopsis'), '(FILMAFFINITY)');
		$data['Fa Critics'] = $this->format->critics($this->format->getValueIfExist($table2, 'Críticas'), $data['Fa Id']);

		if ($data['Fa Duration'] < 30) return ['reject' => 'no son películas'];

		/*
	    |--------------------------------------------------------------------------
	    | THEMOVIEDB
	    |--------------------------------------------------------------------------
	    */

	    /* BÚSQUEDA EN TMDB DEL ID */
	    $data['Tmdb Id'] = $this->format->searchTmdbId($data['Fa Id'], $data['Fa Title'], $data['Fa Original'], $data['Fa Year']);
	    if (is_null($data['Tmdb Id'])) {
	    	if ($data['Fa Count'] > 300) {
	    		return ['error' => $data['Fa Id'] . ' : No se encuentra en Themoviedb'];
	    	} else {
	    		return ['reject' => $data['Fa Id'] . ' : No se encuentra en Themoviedb pero tienen muy pocos votos, las dejamos fuera'];
	    	}
	    } 

	    /* LLAMADA AL API DE TMDB PARA EL RESTO DE DATOS */
		$tmdbapi = file_get_contents('https://api.themoviedb.org/3/movie/' . $data['Tmdb Id'] . '?api_key=2d6ee6298dd2dc10ef74cc25e1b0fc7c&language=es&append_to_response=credits');
		$tmdb = json_decode($tmdbapi, true);

/*		$data['Tmdb Title'] = $tmdb['title'];
		$data['Tmdb Original'] = $tmdb['original_title'];*/
		$data['Tmdb Revenue'] = $tmdb['revenue'];
		$data['Tmdb Budget'] = $tmdb['budget'];
		$data['Tmdb Credits'] = $tmdb['credits'];
		$data['Tmdb Genres'] = $tmdb['genres'];	
		$data['Imdb Id'] = $this->format->getValueIfExist($tmdb, 'imdb_id');
		$data['Tmdb Review'] = $this->format->getValueIfExist($tmdb, 'overview');
		$data['Tmdb Poster'] = $this->format->getValueIfExist($tmdb, 'poster_path');
		$data['Tmdb Background'] = $this->format->getValueIfExist($tmdb, 'backdrop_path');

		/*
	    |--------------------------------------------------------------------------
	    | IMDB
	    |--------------------------------------------------------------------------
	    */

	    /* SI NO EXISTE ID DE IMDB TERMINAMOS*/
	    if (is_null($data['Imdb Id'])) return $data;


		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => 'http://www.omdbapi.com/?i=' . urlencode($data['Imdb Id']) . '&plot=full&apikey=67b952f9',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 10,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "{}",
		]);

		$response = curl_exec($curl);
		$error = curl_error($curl);

		if ($error) {
			$response = curl_exec($curl);
			$error = curl_error($curl);
			if ($error) {
				echo 'error' . $data['Fa Id'] . ' : Error al scrapear de omdb';
				return ['error' => $data['Fa Id'] . ' : Error al scrapear de omdb'];
			}
		}

		curl_close($curl);
		$imdb = json_decode($response, true);

        /* SI LA RESPUESTA DEL OMDB API DA FALSE TAMBIEN TERMINAMOS */
        if ($imdb['Response'] == false) return $data;

        if (isset($imdb['imdbRating']) && $imdb['imdbRating'] != 'N/A')
        	$data['Imdb Rating'] = $this->format->float($imdb['imdbRating']);

        if (isset($imdb['imdbVotes']) AND $imdb['imdbVotes'] != 'N/A')
        	$data['Imdb Count'] = $this->format->integer($imdb['imdbVotes']);

        foreach ($imdb['Ratings'] as $ratings) {
        	if ($ratings['Source'] == 'Rotten Tomatoes') {
        		$data['Rt Rating'] = $this->format->integer($ratings['Value']);
        	}
        }

        /*if (isset($imdb['Ratings']['Source']) AND $imdb['tomatoMeter'] != 'N/A')
        	$data['Rt Rating'] = $this->format->float($imdb['tomatoMeter']);*/

        if (isset($imdb['tomatoURL']) AND $imdb['tomatoURL'] != 'N/A')
        	$data['Rt Url'] = $imdb['tomatoURL'];

		return $data;
	}





}
