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

        $date = Carbon::now()->toDateString();
        $client = new Client();

        //RECORREMOS CADA CANAL
        foreach (config('movies.channels') as $channel) {
        	$url = 'http://www.movistarplus.es/guiamovil/' . $channel . '/' . $date;
        	$crawler = $client->request('GET', $url);

        	//SI HAY ERROR
        	if ($client->getResponse()->getStatus() !== 200) {
			return view('icback.error', ['message' => 'La url generada <a href="' . $url . '">' . $url . '</a> no es válida y devuelve un error ' . $client->getResponse()->getStatus()]);
			} 

			//SCRAPEAMOS PAGINA DEL CANAL (MOVISTAR)
			$data = $this->getPage($crawler, $date);
        }
    }

    public function initSingle()
    {
    	$date = Carbon::now()->toDateString();
    	$client = new Client();
    	$url = 'http://www.movistarplus.es/guiamovil/MV3';
    	$crawler = $client->request('GET', $url);

    	//SI HAY ERROR
    	if ($client->getResponse()->getStatus() !== 200) {
		return view('icback.error', ['message' => 'La url generada <a href="' . $url . '">' . $url . '</a> no es válida y devuelve un error ' . $client->getResponse()->getStatus()]);
		} 

		//SCRAPEAMOS PAGINA DEL CANAL (MOVISTAR)
			$data = $this->getPage($crawler, $date);
    }

	public function getPage($crawler, $date)
	{

		dd($crawler);
		dd(count($crawler->filter('.container_box g_CN')));

		//RECORREMOS FILAS
		$crawler->filter('.container_box g_CN')->each(function($node, $i) {
			dd($node);
		});
		dd('no entra');

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