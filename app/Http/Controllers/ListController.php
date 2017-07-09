<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\buildRepository;
use App\Repositories\showRepository;

use Auth;

class ListController extends Controller
{
	private $build;
	private $show;

	public function __Construct(buildRepository $build, showRepository $show)
	{
		$this->build = $build;
		$this->show = $show;
	}
    
	public function newList(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        }

        $this->validate($request, [
	        'name' => 'required|max:64',
	        'description' => 'max:2000',
	        'ordered' => 'boolean'
	    ],[
	    	'name.required' => 'Introduce un nombre para tu lista',
	    	'name.max' => '64 carácteres máximo',
	    	'description.max' => '2000 carácteres máximo',
	    	'ordered.boolean' => 'valor de lista numerada incorrecto'
	    ]);

	    $state = $this->build->newList($request->input('name'), $request->input('movie'), $request->input('ordered'), $request->input('description'), Auth::user()->id, $request->input('position'));
	    $state['slugname'] = str_slug($request->input('name'), '-');

	    return response()->json($state);
	}

	/*updated*/
	public function addList(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        }

        $this->validate($request, [
	        'list' => 'required|integer',
	        'movie' => 'required|integer',
	        'ordered' => 'required|boolean'
	    ]);

	    $this->build->addList($request->input('list'), $request->input('movie'));

	    return response()->json(['success' => true, 'message' => 'correcto']);		
	}

	public function show($name, $id, Request $request)
	{
		//SI VIENE FILTRADO
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
			$list = $this->show->showFilterList($id, $filters);
		} else {
			$filters = null;
			$list = $this->show->showList($id, $name);
		}

		$listLiked = $this->show->listLikedByUser($id, Auth::id());

		return view('pages.list', compact('lists', 'list', 'filters', 'listLiked'));		
	}

	public function editList($name, $id)
	{
		$list = $this->show->showList($id, $name);
		if(Auth::id() != $list->user_id) return redirect()->route('home');
		return view('pages.editlist', compact('lists', 'list'));			
	}

	public function postEdit(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        }
	    $this->build->updateList($request->input('name'), $request->input('description'), $request->input('ordered'), $request->input('id'));		
	}

	public function extractList(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        } 
        $del = $this->build->extractList($request->input('list'), $request->input('movie'));
        return $del;
	}

	public function deleteList(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        } 
        $del = $this->build->deleteList($request->input('listid'));
        return $del;
	}


	public function addToMylists(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        } 
        $this->build->addToMylists($request->input('list'));

	}

	public function DelFromMylists(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        } 
        $this->build->delFromMylists($request->input('list'));

	}

	public function likeList(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        } 
        $this->build->likeList($request->input('id'), Auth::user()->id);
	}

	public function dislikeList(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        } 
        $this->build->dislikeList($request->input('id'), Auth::user()->id);
	}

}
