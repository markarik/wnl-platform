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

Route::get('/payment/step1', 'Payment\StepOneController@index');
Route::get('/payment/step2', 'Payment\StepTwoContoller@index');
Route::post('/payment/step2', 'Payment\StepTwoContoller@handle');
Route::get('/payment/step3', 'Payment\PaymentController@step3');

Route::group(['middleware' => 'auth'], function () {

	Route::get('/', function() { return redirect('/course/1'); });

	Route::get('/course/1', 'Course\CourseController@index');

	//Route::get( '/course/1/module/{moduleId}/chapter/{chapterId}', 'ModuleController@index' );

	Route::get('/course/1/module/{moduleId}/chapter/{chapterId}/section/{sectionId}', 'ChapterController@index');

	Route::get('/dashboard', 'User\DashboardController@index');

});