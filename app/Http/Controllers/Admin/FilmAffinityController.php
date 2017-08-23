<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Goutte\Client;
use Carbon\Carbon;
use App\Libraries\Format;
use App\Libraries\movieScrap;
use App\Libraries\movistarScrap;
use App\Repositories\scrapRepository;

class FilmAffinityController extends Controller
{

	private $format;
	private $movieScrap;
	private $scrapRepository;

	public function __Construct(Format $format, MovieScrap $movieScrap, MovistarScrap $movistarScrap, ScrapRepository $scrapRepository)
	{
		$this->format = $format;
		$this->movieScrap = $movieScrap;
		$this->movistarScrap = $movistarScrap;
		$this->scrapRepository = $scrapRepository;
		set_time_limit(14400);

		//OJOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO .> DESACTIVAMOS PQ EN EL CURRO NO FUNCIONA, VOLVER A ACTIVAR!!!!!!!!!!!!!
		//ACTUALIZAMOS TABLA GÉNEREOS
		$this->updateAllGenres();

	}

	public function index()
	{
		return view('icback.home');
	}

	public function fromFaId(Request $request)
	{
		/*$time_start = microtime(true); */
		$this->validate($request, [
	        'id' => 'required|integer|digits_between:1,10'
	    ]);

	    //ANTES DE ENTRAR EN FILMAFFINITY HABRÍA QUE VERIFICAR SI YA LA TENEMOS EN DB Y DESDE CUANDO

		//CONSTRUIMOS URL DE FICHA DE FILMAFFINITY A RAIZ DE SU ID DE FILMAFFINITY
		$url = 'http://www.filmaffinity.com/es/film' . $request->input('id') . '.html';
		$client = new Client();
		$crawler = $client->request('GET', $url);

		if ($client->getResponse()->getStatus() !== 200) {
			return view('icback.error', ['message' => 'La url generada <a href="' . $url . '">' . $url . '</a> no es válida y devuelve un error ' . $client->getResponse()->getStatus()]);
		} 

		//LLAMAMOS A LA CLASE QUE SCRAPEA TODOS LOS DATOS DE LA PELÍCULA
		$data = $this->movieScrap->getMovie($crawler);

		//SI DEVUELVE UN ERROR
/*		if (array_key_exists('error', $data)) {
			$log = new \Monolog\Logger(__METHOD__);
			$log->pushHandler(new \Monolog\Handler\StreamHandler(storage_path().'/logs/scrapingerrors.log'));
			$log->addInfo('error en ' . $url . ' :' . $data['error']);
			return view('icback.error', ['message' => 'error en ' . $url . ' :' . $data['error']]);
		}*/

		//ERROR AL SCRAPEAR LA PELICULA
		if (array_key_exists('error', $data)) {
			echo 'error' . $data['error'] . '<br>';
			$this->errorReport($data['error'], $count = 0); //con 0 nunca pasará al registro
			return;

		//PELÍCULAS RECHAZADAS
		} elseif (array_key_exists('reject', $data)) {
			echo 'error 2' . $data['reject'] . '<br>';
			$this->errorReport($data['reject'], $count = 0); //con 0 nunca pasará al registro
			return;

		//OK A GUARDAR
		} else {
			$movie = $this->scrapRepository->build($data);
		}


		return redirect()->route('show', ['slug' => $movie->slug]);
	}

	public function fromFaLetter(Request $request)
	{
		//CONSTRUIMOS URL DE FICHA DE FILMAFFINITY 
		$url = 'http://www.filmaffinity.com/es/allfilms_' . $request->input('letter') . '_' . $request->input('first-page') . '.html';
		$client = new Client();
		$crawler = $client->request('GET', $url);

		if ($client->getResponse()->getStatus() !== 200) {
			return view('icback.error', ['message' => 'La url generada <a href="' . $url . '">' . $url . '</a> no es válida y devuelve un error ' . $client->getResponse()->getStatus()]);
		} 

		for ($i=1; $i<=$request->input('total-pages'); $i++) {

			echo 'scrapeamos pagina: ' . $crawler->getUri() . '<br>';

			//SCRAPEAMOS PÁGINA
			$crawler->filter('.movie-card')->each(function($element) use($client) {

				//SCRAPEAMOS MOVIE CARD
				$card = $this->cardScrap($element);

				//SI YA EXISTE SALIMOS
				if (!$this->scrapRepository->checkUpdated($card['id'], $days=60)) return;

				//SI TIENE 10 O MENOS VOTOS SALIMOS
				if ($card['countScore'] < 10) return;

				//SI ES UNA SERIE O UN CORTO SALIMOS
				if (preg_match('(\(Serie de TV\)|\(C\))', $card['title'])) return;

				//CLICK Y ENTRAMOS
				$crawler = $client->click($card['href']->link());

				echo 'entramos en ' . $crawler->getUri() . '<br>';
					
				//SCRAPEAMOS PELICULA
				$data = $this->movieScrap->getMovie($crawler);
				
				//ERROR AL SCRAPEAR LA PELICULA
				if (array_key_exists('error', $data)) {
					echo 'error' . $data['error'] . '<br>';
					$this->errorReport($data['error'], $card['countScore']);

				//PELÍCULAS RECHAZADAS
				} elseif (array_key_exists('reject', $data)) {
					echo 'error 2' . $data['reject'] . '<br>';
					//hacer algo con las rechazadas?

				//OK AL GUARDAR LA PELICULA
				} else {
					$movie = $this->scrapRepository->build($data);
				}

			});

			//AVANZAMOS PÁGINA
			if ($crawler->filter('.pager .current')->nextAll()->count()) {
                $upPage = $crawler->filter('.pager .current')->nextAll()->link();
                $crawler = $client->click($upPage);             
            //SI YA NO HAY MAS SALIMOS DEL BUCLE FOR
            } else {
            	break;
            }
		}

        echo '<br><br>terminado<br><br>';
	}

	public function fromCartelera()
	{
		$url = 'http://www.filmaffinity.com/es/rdcat.php?id=new_th_es';
		$client = new Client();
		$crawler = $client->request('GET', $url);

		if ($client->getResponse()->getStatus() !== 200) {
			return view('icback.error', ['message' => 'La url generada <a href="' . $url . '">' . $url . '</a> no es válida y devuelve un error ' . $client->getResponse()->getStatus()]);
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

				echo 'entramos en ' . $crawler->getUri() . '<br>';

				//SCRAPEAMOS PELICULA
				$data = $this->movieScrap->getMovie($crawler);

				//ERROR AL SCRAPEAR LA PELICULA
				if (array_key_exists('error', $data)) {
					echo 'error' . $data['error'] . '<br>';
					$this->errorReport($data['error'], $card['countScore']);

				//PELÍCULAS RECHAZADAS
				} elseif (array_key_exists('reject', $data)) {
					echo 'error 2' . $data['reject'] . '<br>';
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

		echo '<br><br>terminado<br><br>';
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

    //CALCULA EL AVG DE TODAS LAS PELÍCULAS DE NUESTRA BD
    public function avg()
    {
    	$this->scrapRepository->avgStars();
    }

    //CALCULA LOS DIFERENTES PAISES QUE EXISTEN
    public function countries()
    {
    	$this->scrapRepository->countries();
    }

    public function fromMovistar(movistarScrap $movistarScrap)
    {
    	$movistarScrap->init();
    }

    public function fromMovistarUrl(Request $request)
    {
    	$client = new Client();
    	$url = $request->input('url');
    	$crawler = $client->request('GET', $url);

    	$tokens = explode('/', $url);
		dd($tokens[sizeof($tokens)-2]);

    	//SI HAY ERROR
    	if ($client->getResponse()->getStatus() !== 200) {
		return view('icback.error', ['message' => 'La url generada <a href="' . $url . '">' . $url . '</a> no es válida y devuelve un error ' . $client->getResponse()->getStatus()]);
		} 

		//SCRAPEAMOS PAGINA DEL CANAL (MOVISTAR)
			$data = $this->movistarScrap->getPage($crawler, $date);
    }

    public function fromMovistarSingle(movistarScrap $movistarScrap)
    {
    	$movistarScrap->initSingle();
    }


}
