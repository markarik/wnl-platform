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

Route::get( '/', function () { return redirect('/course/1'); } );

Route::get( '/course/{cid}', '' );

Route::get( '/course/1/module/{mid}', '' );

Route::get( '/course/1/module/{mid}/topic/{tid}', '' );

