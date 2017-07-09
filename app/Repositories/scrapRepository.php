<?php

namespace App\Repositories;

use App\Entities\Character;
use App\Entities\Comment;
use App\Entities\Critic;
use App\Entities\Genre;
use App\Entities\Listing;
use App\Entities\Movie;
use App\Entities\MovistarSchedule;
use App\Entities\Param;
use App\Entities\User;
use App\Entities\Theatre;

Use App\Libraries\Images;
use Carbon\Carbon;

class ScrapRepository {

    private $images;

    public function __Construct(Images $images)
    {
        $this->images = $images;
    }

    public function build($data)
    {

        /*
        |--------------------------------------------------------------------------
        | MOVIE
        |--------------------------------------------------------------------------
        */

        $movie = Movie::firstOrNew(['fa_id' => $data['Fa Id']]);

        $movie->title            = $data['Fa Title'];
        if (!$movie->exists) { /*solo recalculamos slugs para nuevas películas*/
            $movie->slug             = $this->setSlug($data['Fa Title']);
        }
        $movie->original_title   = $data['Fa Original'];
        $movie->country          = $data['Fa Country'];
        $movie->duration         = $data['Fa Duration'];
        $movie->review           = $data['Tmdb Review'] ? $data['Tmdb Review'] : $data['Fa Review'];
        $movie->fa_id            = $data['Fa Id'];
        $movie->tm_id            = $data['Tmdb Id'];
        $movie->year             = $data['Fa Year'];
        $movie->imdb_id          = $data['Imdb Id'];
        $movie->rt_url           = array_key_exists('Rt Url', $data) ? $data['Rt Url'] : null;
        $movie->fa_rat           = $data['Fa Rating'];
        $movie->fa_rat_count     = $data['Fa Count'];
        $movie->im_rat           = array_key_exists('Imdb Rating', $data) ? $data['Imdb Rating'] : null;
        $movie->im_rat_count     = array_key_exists('Imdb Count', $data) ? $data['Imdb Count'] : null;
        $movie->rt_rat           = array_key_exists('Rt Rating', $data) ? $data['Rt Rating'] : null;
        $movie->rt_rat_count     = array_key_exists('Rt Count', $data) ? $data['Rt Count'] : null;
        $movie->revenue          = $data['Tmdb Revenue'];
        $movie->budget           = $data['Tmdb Budget'];

        /*
        |--------------------------------------------------------------------------
        | AVERAGE
        |--------------------------------------------------------------------------
        */

        if ($movie->im_rat) {
            if ($movie->rt_rat) {
                $value = $this->stars((int)(($movie->im_rat + $movie->rt_rat / 10 + $movie->fa_rat) / 3));
            } else {
                $value = $this->stars((int)(($movie->im_rat + $movie->fa_rat) / 2));
            }
        } else {
            $value = $this->stars((int)$movie->fa_rat);
        }
        $movie->avg = $value;

        /*
        |--------------------------------------------------------------------------
        | IMAGES
        |--------------------------------------------------------------------------
        */

        $validPoster = $validBackground = '';

        //SI MOVIESCRAPER HA RETORNADO UN POSTER:
        if (isset($data['Tmdb Poster'])) {

            //SI LA PELÍCULA YA EXISTE Y TIENE POSTER EN DB
            if (isset($movie->poster)) {
                //¿HA CAMBIADO EL NOMBRE DEL FICHERO O ERA NULL ANTES?
                if($movie->poster != $data['Tmdb Poster']) {
                    $validPoster = $this->images->savePoster($data['Tmdb Poster'], $movie->slug);
                } 
            //SI ES NUEVA O SI YA EXISTE PERO TENÍA POSTER = NULL
            } else {
                $validPoster = $this->images->savePoster($data['Tmdb Poster'], $movie->slug);
            }

            if ($validPoster == 'saved') {
                $movie->check_poster = 1;
                $movie->poster = $data['Tmdb Poster'];
            } elseif ($validPoster == 'error') {
                $movie->check_poster = 0;
                $movie->poster = 'null';
            }
        }

        //SI MOVIESCRAPER HA RETORNADO UN BACKGROUND:
        if (isset($data['Tmdb Background'])) {

            //SI LA PELÍCULA YA EXISTE Y TIENE BACKGROUND EN DB
            if (isset($movie->background)) {
                if($movie->background != $data['Tmdb Background']) {
                    $validBackground = $this->images->saveBackground($data['Tmdb Background'], $movie->slug);
                } 
            //SI ES NUEVA O SI YA EXISTE PERO TENÍA BACKGROUND = NULL
            } else {
                $validBackground = $this->images->saveBackground($data['Tmdb Background'], $movie->slug);
            }

            if ($validBackground == 'saved') {
                $movie->check_Background = 1;
                $movie->Background = $data['Tmdb Background'];
            } elseif ($validBackground == 'error') {
                $movie->check_Background = 0;
                $movie->Background = 'null';
            }
        }

        //GUARDAMOS TODO
        $movie->save();


        /*
        |--------------------------------------------------------------------------
        | CHARACTERS
        |--------------------------------------------------------------------------
        */

        $movie->characters()->detach();

        foreach($data['Tmdb Credits']['cast'] as $i => $cast) {
            //GUARDAMOS ACTOR
            $character = Character::firstOrNew(['id' => $cast['id']]);
            $character->id             = $cast['id'];
            $character->name           = $cast['name'];
            $character->department     = 'actor';
            $character->photo          = $cast['profile_path'];
            $character->save();
            //GUARDAMOS EN ARRAY LISTO PARA SINCRONIZAR DESPUES
            $sync[$cast['id']] = ['order' => $cast['order']];
            //GUARDAMOS IMAGEN SI TIENE
            if ($cast['profile_path']) {
                $this->images->saveCredit($cast['profile_path'], $cast['name'], $movie->id);
            }
        }

        foreach($data['Tmdb Credits']['crew'] as $i => $crew)
        {
            //SOLO GURADAMOS DIRECTOR
            if($crew['job'] == 'Director') {
                $character = Character::firstOrNew(['id' => $crew['id']]);
                $character->id             = $crew['id'];
                $character->name           = $crew['name'];
                $character->department     = 'director';
                $character->photo          = $crew['profile_path'];
                $character->save();
                //GUARDAMOS EN ARRAY LISTO PARA SINCRONIZAR DESPUES
                $sync[$crew['id']] = ['order' => -1];
                //GUARDAMOS IMAGEN SI TIENE
                if ($crew['profile_path']) {
                    $this->images->saveCredit($crew['profile_path'], $crew['name'], $movie->id);
                }
            }
        }

        //SINCRONIZAMOS TABLA PIVOTE
        if (isset($sync)) {
            $movie->characters()->sync($sync);
        }

        /*
        |--------------------------------------------------------------------------
        | GENRES
        |--------------------------------------------------------------------------
        */

        //EXTRAEMOS LA COLUMNA ID DEL ARRAY GENRES
        $values = array_column($data['Tmdb Genres'], 'id');
        if (in_array(10769, $values)) {
            $filter = array(10769);
            $values = array_diff($values, $filter);
        }
        //SINCRONIZAMOS, LOS QUE NO ESTEN EN VALUES SE ELIMINARÁN
        $movie->genres()->sync($values);

        /*
        |--------------------------------------------------------------------------
        | CRITICS
        |--------------------------------------------------------------------------
        */

        if ($data['Fa Critics']) {
            //BORRAMOS CRITICAS PREVIAS
            $movie->critics()->delete();
            //GUARDAMOS LAS NUEVAS
            foreach ($data['Fa Critics'] as $faCritic) {
                if (strlen($faCritic['author']['name']) < 40 AND strlen($faCritic['author']['alias']) < 40) { //puntualmente alguna critica llega mal screpeada con texto en el ext_author, la saltamos
                    $critic = new Critic;
                    $critic->text           = $faCritic['content'];
                    $critic->ext_author     = $faCritic['author']['name'];
                    $critic->ext_media      = $faCritic['author']['alias'];
                    $critic->movie_id       = $movie->id;
                    $critic->user_id        = 2;
                    $critic->save();   
                }
            }
        }

        return $movie;
    }

    public function setSlug($slug)
    {
        $slug = str_slug($slug, '-');
        $count = Movie::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
        /*if ($slug == 'walker') dd($count);*/
        return $count ? "{$slug}-{$count}" : $slug;
    }

    //ACTUALIZA TODOS LOS GENEROS
    public function updateAllGenres($apiGenres)
    {
        foreach($apiGenres as $genre) {
            Genre::firstOrCreate($genre);
        }
    }

    public function setParams($name, $value=NULL, $date=NULL) 
    {
        //SI EXISTE UNA FILA CON EL NOMBRE QUE VAMOS A GUARDAR, ANTES LA BORRAMOS
        $old = Param::where('name', $name);
        if ($old->count() > 0) {
            $old->delete();
        }

        $param = New Param;
        $param->name = $name;
        $param->value = $value;
        $param->date = $date;
        $param->save();
    }

    public function theatres($theatres)
    {
        Theatre::truncate();
        Theatre::insert($theatres);
    }

    public function checkUpdated($id, $days)
    {
        $movie = Movie::where('fa_id', $id)->where('updated_at', '<', Carbon::now()->subDays($days))->first();
        //false = LA PELICULA YA EXISTE Y ES VALIDA. true = NO EXISTE O NO ES VÁLIDA Y HAY QUE AÑADIRLA
        if ($movie) return false;
        return true;
    }

    public function resetMovistar()
    {
        if (MovistarSchedule::where('time', '<', Carbon::now()->subHour())->count()) {
            MovistarSchedule::where('time', '<', Carbon::now()->subHour())->delete();
        }
    }

    public function searchByTitle($title)
    {
        //BUSCAMOS POR TITULO EXACTO
        $movie = Movie::where('title', $title)->get();
        if ($movie->count() == 1) return $movie->first();

        //BUSCAMOS POR TITULO EXACTO SIN PARÉNTESIS
        if (strpos($title, '(') !== FALSE) { 
            $title = trim(preg_replace("/\([^)]+\)/","",$title));
            $movie = Movie::where('title', $title)->get();
            if ($movie->count() == 1) return $movie->first();
        }

        //SI NO SE ENCUENTRA DEVOLVEMOS NULL
        return NULL;
    }

    public function searchByDetails($movistarTitle, $movistarOriginal, $movistarYear)
    {
        $cycle = [$movistarYear - 1, $movistarYear + 1];

        //BUSCAMOS POR LIKE
        $movie = Movie::where('title', 'like', '%' . $movistarTitle . '%')
            ->whereBetween('year', $cycle)
            ->get();
        if ($movie->count() == 1) return $movie->first();

        //SI HAY PARÉNTESIS LOS QUITAMOS Y VOLVEMOS A BUSCAR
        if (strpos($movistarTitle, '(') !== FALSE) { 
            $movistarTitleNoBrackets = trim(preg_replace("/\([^)]+\)/","",$movistarTitle));
            $movie = Movie::where('title', 'like', '%' . $movistarTitleNoBrackets . '%')
                ->whereBetween('year', $cycle)
                ->get();
            if ($movie->count() == 1) return $movie->first();
        }

        //SI NO BUSCAMOS POR EXACTO
        $movie = Movie::where('title', $movistarTitle)
            ->whereBetween('year', $cycle)
            ->get();
        if ($movie->count() == 1) return $movie->first();

        if($movistarOriginal && $movistarOriginal != $movistarTitle) {
            //SI NO BUSCAMOS POR ORIGINAL CON LIKE
            $movie = Movie::where('original_title', 'like', '%' . $movistarOriginal . '%')
                ->whereBetween('year', $cycle)
                ->get();
            if ($movie->count() == 1) return $movie->first();

            //SI NO BUSCAMOS POR ORIGINAL EXACTO
            $movie = Movie::where('original_title', $movistarOriginal)
                ->whereBetween('year', $cycle)
                ->get();
            if ($movie->count() == 1) return $movie->first();
        }

        //SI NO SE ENCUENTRA DEVOLVEMOS NULL
        return NULL;

    }

    /*
    |--------------------------------------------------------------------------
    | AVERAGES
    |--------------------------------------------------------------------------
    */

    //CONVERTIMOS ANTIGUO 0...10 AVG AL NUEVO 0...5 AVG DE TODAS LAS PELICULAS
/*    public function avgStars()
    {
        Movie::chunk(500, function ($movies) {
            $i = 0;
            foreach ($movies as $movie) {
                $i++;
                $value = $this->stars($movie->avg);
                $movie->avg = $value;
                $movie->save();
            }
            echo 'terminado: ' . $i;
        });        
    }*/

    public function stars($value)
    {
        switch (true) {
            case ($value >= 8): return 5;
            case ($value >= 7): return 4;
            case ($value >= 6): return 3;
            case ($value >= 5): return 2;
            case ($value >= 4): return 1;
            default: return 0;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | COUNTRIES
    |--------------------------------------------------------------------------
    */

    public function countries()
    {
        /*$countries = Movie::where('country', 'Alemania del Oeste (RFA)')->update(['country' => 'Alemania']);*/

        /*Movie::chunk(500, function ($movies) {
            foreach ($movies as $movie) {
                $movie->country = str_slug($movie->country);
                $movie->save();
            }
        }); */

        $countries = Movie::groupBy('country')->pluck('country');
        dd($countries);
    }

}
