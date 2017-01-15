<?php

use Illuminate\Support\Facades\Input;
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

Route::get('/payment/step3', 'Payment\StepThreeController@index');
Route::post('/payment/step3', 'Payment\StepThreeController@handle');

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', function () {
        return redirect('/course/1');
    });

    Route::get('/course/1', 'Course\CourseController@index');

    //Route::get( '/course/1/module/{moduleId}/chapter/{chapterId}', 'ModuleController@index' );

    Route::get('/course/1/module/{moduleId}/chapter/{chapterId}/section/{sectionId}', 'ChapterController@index');

    Route::get('/dashboard', 'User\DashboardController@index');
    Route::get('/profile/orders', 'User\OrderController@index');

    /*
    * Ajax common route
    */
    Route::match(['get', 'post'], '/ax', function () {
        abort_unless(Input::has('controller') && Input::has('method'), 404);
        $controller = Input::get('controller');
        $method = Input::get('method');

        return App::make('App\\Http\\Controllers\\Ajax\\' . $controller)->{$method}();
    });

});
