<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', [
    'as'   => 'root',
    'uses' => 'HomeController@statAction'
]);

Route::get('/stat', [
    'as'   => 'base',
    'uses' => 'HomeController@statAction'
]);

Route::get('/set', [
    'as'   => 'the_set',
    'uses' => 'HomeController@setAction'
]);

Route::get('/best', [
    'as'   => 'the_best',
    'uses' => 'HomeController@bestAction'
]);

Route::get('/test', [
    'as'   => 'test',
    'uses' => 'HomeController@testAction'
]);

Route::any('/check', [
    'as'   => 'check',
    'uses' => 'HomeController@checkAction'
]);
