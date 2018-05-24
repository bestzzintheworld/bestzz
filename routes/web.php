<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('basic1', function (){
    return 'Hello World';
});
Route::post('basci2',function (){
    return [1, 2, 3];
});
Route::get('basci3',function (){
    return [1, 2, 3];
});
Route::get('name/{name?}/{id?}',function ($name = 'zz',$id = 1){
    return '我是 '.$name.', id :'.$id.'号。';
})->where(array('name'=>'[A-Za-z]+','id'=>'[0-9]+'));

Route::redirect('/here', '/name/zz', 301);

Route::get('/w1', function () {
    return view('welcome1');
});
Route::get('user/{id}', 'UserController@show');

Route::get('query', 'UserController@query');

Route::get('users', 'Auth\LoginController@zz');
Route::get('create', 'ZzController@create');

Route::get('search/{obj}/type/{type}', 'UserController@search');
Route::get('searchex/{obj}/type/{type}/page/{page}', 'UserController@searchex');
Route::get('test', 'UserController@test');
Route::get('test1', 'UserController@test1');
Route::get('test2', 'UserController@test2');
Route::get('test3', 'UserController@test3');
Route::get('test4', 'UserController@test4');
Route::get('tests', 'UserController@tests');
Route::get('testurl', 'UserController@testurl');
Route::get('tswoole', 'UserController@tswoole');

Route::resource('zz', 'ZzController');

Route::get('home', function () {
    return response('Hello World', 200)
        ->header('Content-Type', 'text/plain');
});
Route::get('/photo','PhotoController@index');
Route::get('/query1','UserController@query1');
Route::get('/orm1','ZzController@orm1');
Route::get('/orm2','ZzController@orm2');
Route::get('/hi/index','HiController@index');
Route::get('/hi/testServer','HiController@testServer');
Route::get('/hi/testClient','HiController@testClient');