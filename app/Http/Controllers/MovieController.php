<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

use App\Repositories\showRepository;

class MovieController extends Controller
{
    private $show;

	public function __Construct(showRepository $show)
	{
		$this->show = $show;
	}

	public function home(Request $request)
	{

		$currentDate = $this->show->getParam('Cartelera', 'date');

		//SI ESTÁ FILTRADA
			if ($request->exists('order')) {
				$filters['order'] = $request->input('order');
			}

			if ($request->exists('fromyear') || $request->exists('toyear')) {
				$filters['year']	 = true;
				$filters['fromyear'] = $request->exists('fromyear') ? $request->input('fromyear') : 1900;
	            $filters['toyear']   = $request->exists('toyear') ? $request->input('toyear') : 2019;
			}

			if ($request->exists('fromnote') || $request->exists('tonote')) {
				$filters['note']	 = true;
				$filters['fromnote'] = $request->exists('fromnote') ? $request->input('fromnote') : 0;
	            $filters['tonote']   = $request->exists('tonote') ? $request->input('tonote') : 5;
			}

		if (isset($filters)) {
			$list = $this->show->filterhome($filters);
		} else {
			$filters = null;
			$list = $this->show->home();
		}

		return view('pages.home', compact('list', 'currentDate', 'lists', 'filters', 'popular'));
	}

	public function tv()
	{
		$list = $this->show->tv();
		return view('pages.tv', compact('list'));
	}

	public function tvFilter(Request $request)
	{
		/*validador*/

		$list = $this->show->tvFilter($request);
		return view('pages.tv', compact('list'));
	}

	public function show($slug)
	{
		$movie = $this->show->movieBySlug($slug);
		$directors = $movie->characters()->where('department', 'director')->orderBy('character_movie.order')->get();
		$actors = $movie->characters()->where('department', 'actor')->orderBy('character_movie.order')->get();

		if(Auth::check()){
			$lists = $this->show->listsByUser(Auth::user()->id);
			$movieInList = False;
	        foreach( $lists as $list ){
	            if( $list->movies->where('id', $movie->id) ){
	                $movieInList= True;
	            }
	        }
		}

		return view('pages.single', compact('movie', 'directors', 'actors', 'lists', 'movieInList'));
	}

	public function search(Request $request)
	{
		//Si estamos buscando ya algo
		if ($string = $request->input('search')) {
			$this->validate($request, [
		        'search' => 'required|max:50'
		    ]);
		    $movies = $this->show->normalSearch($string);
		} else {
			$movies = null;
		}

		return view('pages.search', compact('lists', 'movies', 'string'));
	}

	public function liveSearch(Request $request)
    {
    	if( ! $request->ajax()) {       
            return back(); 
        }

        $this->validate($request, [
	        'string' => 'required|max:50'
	    ]);

	    $results = $this->show->liveSearch($request->input('string'));

        if ($results->isEmpty()) {
            return response()->json(['response' => false]);
        }

        return response()->json(['response' => true, 'result' => $results]);
	}

	public function character($name, $id)
	{
		$character = $this->show->character($id);
		return view('pages.character', compact('character', 'lists'));	
	}

	public function userPage($name, $id)
	{
		$user = $this->show->userLists($id, $name);
		return view('pages.userpage', compact('user', 'lists'));	
	}

}
