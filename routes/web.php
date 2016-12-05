<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

Route::get( '/', function () { return redirect('/course/1'); } );

Route::get('/course/1', 'HomeController@index');

//Route::get( '/course/1/module/{moduleId}/chapter/{chapterId}', 'ModuleController@index' );

Route::get( '/course/1/module/{moduleId}/chapter/{chapterId}/section/{sectionId}', 'ChapterController@index' );

