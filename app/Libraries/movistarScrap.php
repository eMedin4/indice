<?php
namespace App\Libraries;

use Carbon\Carbon;
use Goutte\Client;
use App\Libraries\Format;
use App\Repositories\scrapRepository;

class MovistarScrap
{

	private $format;
	private $scrapRepository;

	public function __Construct(Format $format, ScrapRepository $scrapRepository)
	{
		$this->format = $format;
		$this->scrapRepository = $scrapRepository;
	}

	public function init()
    {
    	//BORRAMOS LA LISTA
        $this->scrapRepository->resetMovistar();
        echo 'borrada programacion antigua...' . PHP_EOL;

        $client = new Client();

		$date = Carbon::now()->toDateString();
		$validDate = $this->scrapRepository->getMovistarValidDate($date); //si ya hay peliculas en esta fecha retorna false. Si no hay peliculas returna true
		if ($validDate) {
	        foreach (config('movies.channels') as $channelCode => $channel) {
	        	$url = 'http://www.movistarplus.es/guiamovil/' . $channelCode . '/' . $date;
	        	$crawler = $client->request('GET', $url);
	        	if ($client->getResponse()->getStatus() !== 200) echo $url . ' devuelve error ' . $client->getResponse()->getStatus();
				$this->getPage($client, $crawler, $date, $channelCode, $channel);
	        }
		} else {
			echo 'la fecha ' . $date . ' ya esta descargada' . PHP_EOL;
		}

		$date = Carbon::now()->addDay()->toDateString();
		$validDate = $this->scrapRepository->getMovistarValidDate($date);
		if ($validDate) {
	        foreach (config('movies.channels') as $channelCode => $channel) {
	        	$url = 'http://www.movistarplus.es/guiamovil/' . $channelCode . '/' . $date;
	        	$crawler = $client->request('GET', $url);
	        	if ($client->getResponse()->getStatus() !== 200) echo $url . ' devuelve error ' . $client->getResponse()->getStatus();
				$this->getPage($client, $crawler, $date, $channelCode, $channel);
			}
		} else {
			echo 'la fecha ' . $date . ' ya esta descargada' . PHP_EOL;
		}
    }

    public function initSingle()
    {
    	$client = new Client();

    	$date = Carbon::now()->toDateString();
    	$url = 'http://www.movistarplus.es/guiamovil/MV3/' . $date;
    	$crawler = $client->request('GET', $url);
		$this->getPage($client, $crawler, $date, 'MV3', 'Canal #0');

    	$date = Carbon::now()->addDay()->toDateString();
		$url = 'http://www.movistarplus.es/guiamovil/MV3/' . $date;
    	$crawler = $client->request('GET', $url);
    	$this->getPage($client, $crawler, $date, 'MV3', 'Canal #0');

    	echo 'terminado';
    }

	public function getPage($client, $crawler, $date, $channelCode, $channel)
	{

		//RECORREMOS FILAS
		$crawler->filter('.container_box.g_CN')->each(function($node, $i) use($client, $date, $channelCode, $channel) {

			//SI NO ES CINE DESCARTAMOS
			if ($node->filter('li.genre')->text() != 'Cine') return;
			$title = trim($node->filter('li.title')->text());
			$id = $this->format->movistarId($node->filter('a')->attr('href'));
			$time = $node->filter('li.time')->text();
			$datetime = $this->format->movistarDate($time, $date);
			$splitDay = $this->format->splitDay($date); //6 DE LA MAÑANA DEL DIA $DATE

			//SI LA HORA DE LA PELICULA ES ANTES DE LAS 6:00 (SPLITTIME) Y LA FILA DE LA TABLA ES DESPUES DE LA FILA 6, AÑADIMOS UN DÍA
			if ($datetime < $splitDay && $i > 6) {
				$datetime = $datetime->addDay();
			}
			
			//ANULAMOS SI EL TITULO COINCIDE CON FRASES BANEADAS
			foreach(config('movies.moviesTvBan') as $ban) {
				if (strpos($title, $ban) !== FALSE) {
					return;
				}
			}

			//BORRAMOS PALABRAS BANEADAS DEL TITULO
			$title = str_replace(config('movies.wordsTvBan'), '', $title);

			//BUSCAMOS 1 COINCIDENCIA POR TITULO EXACTO
			$movie = $this->scrapRepository->searchByTitle($title);

			if ($movie) {
				echo 'encontrada ' . $title . PHP_EOL;

			} else  { //NO ENCONTRADAS

				//ENTRAMOS EN LA FICHA
				echo 'entramos en la ficha de ' . $title . PHP_EOL;
				$page = $client->click($node->filter('a')->link());

				//ALGUNAS FICHAS DE 'CINE CUATRO', 'CINE BLOCKBUSTER',.. SIN PELICULA, NO TIENEN AÑO EN LA FICHA, ANULAMOS
				if ($page->filter('p[itemprop=datePublished]')->count() == 0) {
					return;
				}

				//COJEMOS DATOS
				$year = $page->filter('p[itemprop=datePublished]')->attr('content');
				$original = $this->format->getElementIfExist($page, '.title-especial p', NULL);

				//BUSCAMOS CON LOS DATOS
				$movie = $this->scrapRepository->searchByDetails($title, $original, $year);
			}

			if ($movie) {
				$this->scrapRepository->setMovie($movie, $datetime, $channelCode, $channel);
			}

		});

	}


	public function getPage2($crawler, $date)
	{

		//RECORREMOS FILAS
		$file[] = $crawler->filter('li.fila').each(function($element, $i) use($date) {

			//SI NO ES CINE DESCARTAMOS
			if ($element->filter('li.genre')->text() != 'Cine') return;

			$title = trim($element->filter('li.title')->text());
			$id = $this->format->movistarId($element->filter('a')->attr('href'));
			$time = $element->filter('li.time')->text();
			$datetime = $this->format->movistarDate($time, $date);
			$splitDay = $this->format->splitDay($date); //6 DE LA MAÑANA DEL DIA $DATE

			//SI LA HORA DE LA PELICULA ES ANTES DE LAS 6:00 (SPLITTIME) Y LA FILA DE LA TABLA ES DESPUES DE LA FILA 6, AÑADIMOS UN DÍA
			if ($datetime < $splitDay && $i > 6) {
				$datetime = $datetime->addDay();
			}

			//ANULAMOS SI EL TITULO COINCIDE CON FRASES BANEADAS
			foreach(config('movies.moviesTvBan') as $ban) {
				if (strpos($title, $ban) !== FALSE) {
					return;
				}
			}

			//BORRAMOS PALABRAS BANEADAS DEL TITULO
			$title = str_replace(config('movies.wordsTvBan'), '', $title);

			//BUSCAMOS 1 COINCIDENCIA POR TITULO EXACTO
			$movie = $this->scrapRepository->searchByTitle($title);

			//SI NO LO HA ENCONTRADO
			if (!$movie) {

				//ENTRAMOS EN LA FICHA
				echo '<br>entramos en la ficha de ' . $title;
				$page = $client->click($element->filter('a')->link());

				//ALGUNAS FICHAS DE 'CINE CUATRO', 'CINE BLOCKBUSTER',.. SIN PELICULA, NO TIENEN AÑO EN LA FICHA, ANULAMOS
				if ($page->filter('p[itemprop=datePublished]')->count() == 0) {
					return;
				}

				//COJEMOS DATOS
				$year = $page->filter('p[itemprop=datePublished]')->attr('content');
				$original = $this->format->getElementIfExist($page, '.title-especial p', NULL);

				//BUSCAMOS CON LOS DATOS
				$movie = $this->scrapRepository->searchByDetails($title, $original, $year);
			}
		});

		dd($file);
	}
}