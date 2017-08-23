<?php
namespace App\Libraries;

use Goutte\Client;
use App\Libraries\Format;
use App\Libraries\movieScrap;
use App\Repositories\scrapRepository;

class Icback 
{

	private $format;
	private $movieScrap;
	private $scrapRepository;

	public function __Construct(Format $format, MovieScrap $movieScrap, ScrapRepository $scrapRepository)
	{
		$this->format = $format;
		$this->movieScrap = $movieScrap;
		$this->scrapRepository = $scrapRepository;
		set_time_limit(14400);
		$this->updateAllGenres();
	}

	public function fromCartelera() 
	{
		$url = 'http://www.filmaffinity.com/es/rdcat.php?id=new_th_es';
		$client = new Client();
		$crawler = $client->request('GET', $url);

		if ($client->getResponse()->getStatus() !== 200) {
			//$this->error('La url generada no es válida: ' . $client->getResponse()->getStatus() . ' ' . $url);
			echo 'La url generada <a href="' . $url . '">' . $url . '</a> no es válida y devuelve un error ' . $client->getResponse()->getStatus() . PHP_EOL;
		} 

		//RECORREMOS LAS SECCIONES DE FECHA
		$count = $crawler->filter('#main-wrapper-rdcat')->count();

		$theatres = [];
		for ($i=0; $i<$count; $i++) {

			//SCRAPEAMOS SECCION $i
			$element = $crawler->filter('#main-wrapper-rdcat')->eq($i);

			//PRIMERA FECHA LA GUARDAMOS COMO FECHA DE CARTELERA
			$dateSection = $this->format->date($element->filter('.rdate-cat')->text());
			if ($i==0) {
				$this->scrapRepository->setParams('Cartelera', NULL, $dateSection);
			} 

			//RECORREMOS LAS PELÍCULAS DE LA SECCION
			$section = $element->filter('.movie-card')->each(function($element) use($client, $i, $dateSection) {

				$card = $this->cardScrap($element);

				$crawler = $client->click($card['href']->link());

				//$this->comment('entramos en ' . $crawler->getUri());
				echo 'entramos en ' . $crawler->getUri() . PHP_EOL;

				//SCRAPEAMOS PELICULA
				$data = $this->movieScrap->getMovie($crawler);

				//ERROR AL SCRAPEAR LA PELICULA
				if (array_key_exists('error', $data)) {
					//$this->error('error' . $data['error']);
					echo 'error: ' . $data['error'] . PHP_EOL;
					$this->errorReport($data['error'], $card['countScore']);

				//PELÍCULAS RECHAZADAS
				} elseif (array_key_exists('reject', $data)) {
					//$this->error('error 2' . $data['reject']);
					echo 'error 2: ' . $data['reject'] . PHP_EOL;
					$this->errorReport($data['reject'], $card['countScore']);


				} else {
					$movie = $this->scrapRepository->build($data);

					//PREPARAMOS ARRAY THEATRES
					$theatres['name'] = $i==0 ? 'Estreno' : '';
					$theatres['date'] = $dateSection;
					$theatres['movie_id'] = $movie->id;
					return $theatres;
				}
			});	

			$theatres = array_merge($theatres, array_filter($section));
		}

		//GUARDAMOS LA LISTA CARTELERA
		$this->scrapRepository->theatres($theatres);

		//$this->info('terminado');
		echo 'terminado' . PHP_EOL;
	}

	public function cardScrap($element)
	{
		$result['href']  = $element->filter('.movie-card h3 a'); 
		$result['id'] = $this->format->faId($result['href']->attr('href'));
		$result['title'] = $element->filter('.movie-card h3 a')->text(); 
		$result['score'] = $this->format->score($element->filter('.avg-rating')->text());
		$result['countScore'] = $this->format->integer($this->format->getElementIfExist($element, '.rat-count', 0));
		return $result;
	}

	public function updateAllGenres()
    {
    	$api = file_get_contents('https://api.themoviedb.org/3/genre/movie/list?api_key=2d6ee6298dd2dc10ef74cc25e1b0fc7c&language=es-ES');
    	$apiGenres = json_decode($api, true);
    	$apiGenres = $this->scrapRepository->updateAllGenres($apiGenres['genres']);
    }

    public function errorReport($error, $count)
    {
    	if ($count > 100 OR $error == 'duration') { //Filtros para pasar al log
			$log = new \Monolog\Logger(__METHOD__);
			$log->pushHandler(new \Monolog\Handler\StreamHandler(storage_path().'/logs/scrapingerrors.log'));
			$log->addInfo('error en:' . $error);
		}
    }


}
