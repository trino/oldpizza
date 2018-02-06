<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
View::composer('*', function($view){
    View::share('view_name', $view->getName());
});

Route::group(['middleware' => ['web']], function () {

    Route::get('/', function () {
        return view("index")->render();
    })->middleware('guest');

    Route::get('/index', function () {
        return view("index")->render();
    })->middleware('guest');

    Route::any('/auth/login',           'AuthController@login');
    Route::any('/auth/gettoken',        'AuthController@gettoken');
    Route::any('/placeorder',           'HomeController@placeorder');

    Route::any('/call',                 'HomeController@robocall');
    Route::any('/test',                 'HomeController@index');
    Route::any('/help',                 'HomeController@help');
    Route::any('/editmenu',             'HomeController@editmenu');

    Route::any('/list/{table}',         'HomeController@tablelist');
    Route::any('/user/info',            'HomeController@edituser');
    Route::any('/user/info/{id}',       'HomeController@edituser');

    Route::any('/edit',                 'HomeController@edit');
    Route::any('/edittable',            'HomeController@edittable');

    Route::auth();
});
