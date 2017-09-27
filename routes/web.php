<?php

/*
	PRIVATE ADMIN
*/

	Route::group(['prefix' => 'icback', 'middleware' => ['auth', 'admin']], function() {
		Route::get('/getalldata', 		['as' => 'getalldata', 	 'uses' => 'Admin\FilmAffinityController@getAllData']);
		Route::get('/', 				['as' => 'icback', 	 'uses' => 'Admin\FilmAffinityController@index']);
		Route::get('/fromfaid', 		['as' => 'fromfaid',  	 'uses' => 'Admin\FilmAffinityController@fromFaId']);
		Route::get('/fromfaletter', 	['as' => 'fromfaletter',  	 'uses' => 'Admin\FilmAffinityController@fromFaLetter']);
		Route::get('/fromcartelera', 	['as' => 'fromcartelera','uses' => 'Admin\FilmAffinityController@fromCartelera']);
		Route::get('/frommovistar', 	['as' => 'frommovistar','uses' => 'Admin\FilmAffinityController@fromMovistar']);
		Route::get('/frommovistarurl', 	['as' => 'frommovistarurl','uses' => 'Admin\FilmAffinityController@fromMovistarUrl']);
		Route::get('/frommovistarsingle', 	['as' => 'frommovistarsingle','uses' => 'Admin\FilmAffinityController@fromMovistarSingle']);
	});


/*
	PRIVATE AUTH
*/

	Route::group(['middleware' => 'auth'], function () {

		//LISTAS
    	Route::get('/lista/{name}/{id}/editar', ['as' => 'editlist', 'uses' => 'ListController@editList']);
		Route::post('/lista/crear', ['as' => 'newlist', 'uses' => 'ListController@newList']);
    	Route::post('/lista/eliminar', ['as' => 'deletelist', 'uses' => 'ListController@deleteList']);
		Route::post('/lista/anadir', ['as' => 'addlist', 'uses' => 'ListController@addList']);
    	Route::post('/lista/sustraer', ['as' => 'extractlist', 'uses' => 'ListController@extractList']);
    	Route::post('/lista/editar', ['as' => 'posteditlist', 'uses' => 'ListController@postEdit']); //editar info
    	//Route::post('/lista/addtomylists', ['as' => 'addtomylists', 'uses' => 'ListController@addToMylists']);
    	//Route::post('/lista/delfrommylists', ['as' => 'delfrommylists', 'uses' => 'ListController@delFromMylists']);

    	//ME GUSTA
    	Route::post('/likelist', ['as' => 'likelist', 'uses' => 'ListController@likeList']);
    	Route::post('/dislikelist', ['as' => 'dislikelist', 'uses' => 'ListController@dislikeList']);
    	//Route::get('/perfil/listas-guardadas/{name}/{id}', ['as' => 'usersavedlists', 'uses' => 'MovieController@userSavedLists']);

    	//LOGOUT
    	Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\SocialController@logout']);

    	//OTROS
		//Route::post('/comment/{id}', ['as' => 'comment', 'uses' => 'MovieController@comment']);

	});


/*
	AUTH
*/

	Route::group(['middleware' => 'guest'], function () {

		//AUTH USER
	    Route::get('/login', ['as' => 'login', 'uses' => 'Auth\SocialController@login']);
	    Route::get('/authsocial/manage/{provider}', ['as' => 'authsocial', 'uses' => 'Auth\SocialController@redirectToProvider']);
	    Route::get('/authsocial/callback/{provider}', 'Auth\SocialController@handleProviderCallback');

	    //AUTH ADMIN
	    Route::get('/login/admin', ['as' => 'loginadmin', 'uses' => 'Auth\SocialController@loginAdmin']);
	    Route::post('/login/admin', ['as' => 'postloginadmin', 'uses' => 'Auth\SocialController@postLoginAdmin']);

	    //OTROS
	    //Route::get('/provisionallogin', ['as' => 'provisionallogin', 'uses' => 'Auth\SocialController@provisionalLogin']);
		//google oauth: https://console.developers.google.com/apis/library?project=indice-cine (elann2013)

	});


/*
	MAIN
*/

	//PRINCIPAL
	Route::get('/', ['as' => 'home', 'uses' => 'MovieController@home']);
	Route::get('/peliculas-hoy-tv', ['as' => 'tv', 'uses' => 'MovieController@tv']);
	
	//VER USUARIO
	Route::get('/usuario/{name}/{id}', ['as' => 'userpage', 'uses' => 'MovieController@userPage']);
	
	//VER LISTA
	Route::get('/lista/{name}/{id}', ['as' => 'list', 'uses' => 'ListController@show']);

	//BUSCAR
	Route::get('/buscar', ['as' => 'search', 'uses' => 'MovieController@search']);
	Route::post('/livesearch', ['as' => 'livesearch', 'uses' => 'MovieController@liveSearch']);
	
	//ACTORES
	Route::get('/ficha/{name}/{id}', ['as' => 'character', 'uses' => 'MovieController@character']);
	
	//PELICULAS
	Route::get('/{slug}', ['as' => 'show', 'uses' => 'MovieController@show']);

	//OTROS
	//Route::get('/television/filtrar', ['as' => 'tvfilter', 'uses' => 'MovieController@tvFilter']);
	//Route::get('/privacidad', function () { return view('pages.privacy'); });

	


/*

//PROVISIONAL
Route::get('avg', ['as' => 'avg', 'uses' => 'Admin\FilmAffinityController@avg']);
Route::get('countries', ['as' => 'countries', 'uses' => 'Admin\FilmAffinityController@countries']);

//POSTER QUE NO SE GUARDA: 2QoLPgwWphEO2eVDm6ehGxCWoXV c9YqifOPRSdSPapWVwRZipwORz7 actor8830


//ARTISAN
Route::get('/artisan', function() {
	//Artisan::call('make:migration', ['name' => 'create_fa_id_index', '--table' => 'movies']);
	//Artisan::call('migrate');
	//Artisan::call('make:model', ['name' => 'Entities/List']);
	Artisan::call('make:provider', ['name' => 'ComposerServiceProvider']);
	dd(Artisan::output());
});


/*
	PARA BORRAR CONTENIDO DE TABLA
		SET FOREIGN_KEY_CHECKS = 0; 
		TRUNCATE characters; 
		SET FOREIGN_KEY_CHECKS = 1;
*/
