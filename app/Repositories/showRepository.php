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

use Carbon\Carbon;

class ShowRepository {

    public function getParam($name, $column)
    {
        return Param::where('name', $name)->value($column);
    }

    public function home()
    {
        return Theatre::join('movies', 'theatres.movie_id', '=', 'movies.id')->orderBy('date', 'desc')->get();
        //return Theatre::with('movie')->get();
        //return Movie::whereHas('theatre', function($q) {
        //    $q->orderBy('movie_id');
        //})->get();
    }

    public function filterHome($filters)
    {
        return Theatre::join('movies', function ($q) use ($filters) {
            $q->on('theatres.movie_id', '=', 'movies.id');

            //SI USAMOS EL FILTRO AÑO
            if (isset($filters['year'])) {
                $q->whereBetween('year', [$filters['fromyear'], $filters['toyear']]);
            }

            //SI USAMOS EL FILTRO PUNTUACIÓN
            if (isset($filters['note'])) {
                $q->whereBetween('avg', [$filters['fromnote'], $filters['tonote']]);
            }

            $q->orderBy('order');
        })->orderBy('date', 'desc')->get();
    }


    public function tv()
    {
        return MovistarSchedule::join('movies', 'movistar_schedule.movie_id', '=', 'movies.id')->where('time', '>', Carbon::now()->subHour())->orderBy('time')->simplePaginate(60);
        //return MovistarSchedule::with('movie')->orderBy('time')->simplePaginate(60);
    }

    public function showFilterList($id, $filters)
    {
        $list = Listing::where('id', $id)->with(['movies' => function($q) use ($filters) {

            //SI USAMOS EL FILTRO AÑO
            if (isset($filters['year'])) {
                $q->whereBetween('year', [$filters['fromyear'], $filters['toyear']]);
            }

            //SI USAMOS EL FILTRO PUNTUACIÓN
            if (isset($filters['note'])) {
                $q->whereBetween('avg', [$filters['fromnote'], $filters['tonote']]);
            }

            $q->orderBy('order');
        }])->first();

        //cojemos el nombre de la lista en la db (convertida a slug) y la comparamos con la de la url, para que sea la misma
        /*if (str_slug($list->name) != $name) {
            abort(404);
        }*/
        return $list;
    }

    public function showList($id, $name)
    {
        //$list = Listinig::join('list_movie', 'lists.id', '=', 'list_movie.list_id')->join('movie', 'list_movie.movie_id', '=', 'movie.id')->where('id', $id)->get();
        $list = Listing::where('id', $id)->with(['movies' => function($q) {
            $q->orderBy('order'); //para ordenar movies por order : Constraining Eager Loads
        }])->first();
        
        //cojemos el nombre de la lista en la db (convertida a slug) y la comparamos con la de la url, para que sea la misma
        //lo eliminamos porque si no no funciona al editar el título de la película en ajax
        /*if (str_slug($list->name) != $name) {
            abort(404);
        }*/
        return $list;
    }

    public function tvFilter($request)
    {
    	return MovistarSchedule::join('movies', 'movies.id', '=', 'movistar_schedule.movie_id')
    		->whereBetween('movies.year', [$request->input('from-year'), $request->input('to-year')])
            ->whereIn('channel_code', $request->input('channel'))
    		->orderBy($request->input('order'), 'desc')
    		->get();
    }

    public function movieBySlug($slug)
    {
        return Movie::where('slug', $slug)->firstOrFail();
    }

    public function character($id)
    {
        return Character::with(['movies' => function($q) {
            $q->orderBy('year', 'desc');
        }])->find($id);
    }

    public function userLists($id, $name)
    {
        $user = User::where('id', $id)->with(['lists' => function($q) {
                $q->orderBy('updated_at', 'desc');
            }, 'lists.movies' => function($q) {
                $q->orderBy('order');
            }
        ])->first();
        if (str_slug($user->name) != $name) {
            abort(404);
        }
        return $user;
    }

    public function listsByUser($user)
    {
        return Listing::where('user_id', $user)->with('movies')->withCount('movies')->orderBy('updated_at', 'desc')->get();
    }

    public function liveSearch($string)
    {
        return Movie::whereRaw("MATCH(title,original_title) AGAINST(? IN BOOLEAN MODE)", 
                array($string))->take(10)->get();
    }

    public function normalSearch($string)
    {
        return Movie::whereRaw("MATCH(title,original_title) AGAINST(? IN BOOLEAN MODE)", 
                array($string))->take(50)->get();
    }

    public function popularLists()
    {
        return Listing::orderBy('relevance', 'desc')->withCount('movies')->take(10)->get();
    }

    public function likeLists($id)
    {
        return User::find($id)->savedLists()->withCount('movies')->get();
    }

    public function listLikedByUser($id, $userId)
    {
        $list = Listing::find($id);
        //si list_user contiene este lista y este user devuelve true
        return $list->likedToUsers->contains($userId);
    }

}
