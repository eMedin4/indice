<?php
namespace App\Libraries;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Format 
{

	/*
    |--------------------------------------------------------------------------
    | FORMAT DE NÚMEROS Y CADENAS
    |--------------------------------------------------------------------------
    */

	// EXTRAE EL ID DE /es/film422703.html
	public function faId($value)
	{
		$value = substr($value, 8); //elimina 8 primeros carácteres
		$value = substr($value, 0, -5); //elimina 5 últimos carácteres
		$value = $this->Integer($value);
		return $value;
	}

	//ELIMINA STRING DE UNA CADENA DE TEXTO VALUE
	public function removeString($value, $string)
	{
		$string = preg_quote($string); //escapa los carácteres necesarios
		return trim(preg_replace('/' . $string . '/', '', $value));
	}

	//QUITA PUNTOS Y OTROS CARÁCTERES Y DEVUELVE EL ENTERO
	public function Integer($value)
	{
		return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
	}


	public function float($value)
	{
		return (float) str_replace(',', '.', $value);
	}

	//LIMPIAMOS EL STRING DE ESPACIOS, SALTOS DE LINEA,...
	public function cleanData($value)
	{
		$value = preg_replace('/\xA0/u', ' ', $value); //Elimina %C2%A0 del principio y resto de espacios
		$value = trim(str_replace(array("\r", "\n"), '', $value)); //elimina saltos de linea al principio y final
		return $value;
	}	

	//SI EXISTE UNA KEY DEVOLVEMOS EL VALUE	
	public function getValueIfExist($array, $key)
	{
		if (array_key_exists($key, $array) AND !empty($array[$key])) {
			return $array[$key];
		} else {
			return NULL;
		}
	}

	/*
    |--------------------------------------------------------------------------
    | MANIPULACIÓN DEL DOM CRAWLER
    |--------------------------------------------------------------------------
    */

	// DEVUELVE EL TEXTO SI EXISTE LA CLASE CSS O EL DEFAULT SI NO
	public function getElementIfExist($element, $class, $default) 
	{
		if ($element->filter($class)->count()) {
			return $element->filter($class)->text(); 
		} else {
			return $default;
		}	
	}

	// VALIDACIÓN PARA LAS CRÍTICAS DE FILMAFFINITY
	public function critics($value, $id)
	{
		/*$value = explode('"', $value);*/
		$value = preg_split("/\\r\\n|\\r|\\n/", $value);
		$value = array_map('trim', $value);
		$value = array_filter($value);
		$value = array_map(function($value) {
			$value = preg_replace('/Mostrar(.*)críticas más/','',$value); //Elimina Mostrar ?? críticas mas
            $value = preg_replace('/Puntuación(.*)\)/','',$value);
            $value = $this->cleanData($value);
			return trim($value);
		}, $value);
		$value = array_filter($value); //Elimina elementos vacíos

		/*Si da impar anulamos*/
		if (count($value) % 2 != 0) {
			$log = new \Monolog\Logger(__METHOD__);
			$log->pushHandler(new \Monolog\Handler\StreamHandler(storage_path().'/logs/scrapingerrors.log'));
			$log->addInfo($id . ': Error al emparejar críticas con autores');
			return NULL;
		}

        $value = array_chunk($value, 2);

        foreach ($value as $critic) {

        	$critic[1] = mb_convert_case($critic[1], MB_CASE_TITLE, "UTF-8"); //Capitalizamos la primera letra con carácteres especiales (acentos)

        	if (substr_count($critic[1], ':') == 1) { //troceamos el autor en nombre y alias, casi siempre lo separa el caracter :
        		$critic[1] = explode(':', $critic[1]);
        		$critic[1] = array_map('trim', $critic[1]);
        	} else { //si en alguna no hay : o hay mas de uno anulamos el corte y en su lugar lo repetimos
        		$critic[1]= [$critic[1], $critic[1]];
        	}

        	$critic[0] = str_replace('"', '', $critic[0]); //eliminamos comillas dobles que tienen todas las criticas al principio y fin
        	if (strlen($critic[0]) > 490) {
        		$critic[0] = substr($critic[0], 0, 490) . '...'; //si es demasiado larga la cortamos
        		$critic[0] = mb_convert_encoding($critic[0], 'UTF-8', 'UTF-8');
        	}

        	//Y construimos un nuevo array con todos los datos
        	$result[] = [
        		'content' 	=> $critic[0],
        		'author'=> [
        			'name'  => $critic[1][0],
        			'alias' => $critic[1][1],
        		]
        	];
        }

        return isset($result) ? $result : NULL;
	}

	//FORMATEA UNA FECHA DESDE EJ. '16 de septiembre de 2016' 
    public function date($value)
    {
    	$value = urldecode(str_replace('%E2%80%83', '', urlencode($value)));
    	if($value == 'Sin fecha confirmada') {
    		return NULL;
    	}
    	preg_match_all('/(\d)|(\w)/', $value, $matches);
    	$numbers = implode($matches[1]);
    	$year = substr($numbers, -4);
    	$day = substr($numbers, 0, -4);
		$letters = implode($matches[2]);
		$month = substr(substr($letters, 0, -2), 2);
		$translateMonth = [
			'enero'		=> '01',
			'febrero'	=> '02',
			'marzo'		=> '03',
			'abril'		=> '04',
			'mayo'		=> '05',
			'junio'		=> '06',
			'julio'		=> '07',
			'agosto'	=> '08',
			'septiembre'=> '09',
			'octubre'	=> '10',
			'noviembre'	=> '11',
			'diciembre'	=> '12',
		];
		$value = $year . '-' . $translateMonth[$month] . '-'. $day;
    	return $value;
    }

    public function score($value) {
    	if ($value == '--') {
    		return null;
    	}
    	return $this->float($value);
    }


	/*
    |--------------------------------------------------------------------------
    | FUNCIONES DE LA API DE THEMOVIEDB
    |--------------------------------------------------------------------------
    */

	public function searchTmdbId($faId, $faTitle, $faOriginal, $faYear)
	{

		if (array_key_exists($faId, config('movies.verified'))) {
			return config('movies.verified')[$faId]; //ID DE VERIFICADAS
		}

		$search = $this->apiTmdbId($faTitle, $faYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}

		$search = $this->apiTmdbId($faOriginal, $faYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}
		
		$fwYear = $faYear + 1;
		$search = $this->apiTmdbId($faTitle, $fwYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}

		$search = $this->apiTmdbId($faOriginal, $fwYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}

		$frYear = $faYear - 1;
		$search = $this->apiTmdbId($faTitle, $frYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}

		$search = $this->apiTmdbId($faOriginal, $frYear);
		if ($search['total_results']) {
			return $search['results'][0]['id'];
		}

    	return null;

	}


	public function apiTmdbId($string, $year)
	{
		$api = file_get_contents('https://api.themoviedb.org/3/search/movie?api_key=2d6ee6298dd2dc10ef74cc25e1b0fc7c&query=' . urlencode($string) . '&year=' . $year . '&language=es');
		return json_decode($api, true);	
	}

	/*
    |--------------------------------------------------------------------------
    | FUNCIONES DEL SCRAPER MOVISTAR
    |--------------------------------------------------------------------------
    */

	// http://www.movistarplus.es/ficha/a-ballerina-s-tale?tipo=E&id=1275910  : ELIMINA TODO MENOS EL ID
	public function movistarId($value)
	{
		$value = explode("&id=", $value);
		return $value[1];
	}

	//$date = 'YYYY-MM-DD'; $time = 09:16;
    public function movistarDate($time, $date)
    {
    	$time = $this->cleanData($time);
    	$time = explode(':', $time);
    	$date = explode('-', $date);
		//año, mes, dia, hora, minuto, segundo, timezone
		return Carbon::create($date[0], $date[1], $date[2], $time[0], $time[1]);
    }

    public function splitDay($date)
    {
    	$date = explode('-', $date);
    	return Carbon::create($date[0], $date[1], $date[2], 6, 00);
    }

}
