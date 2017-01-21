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

Route::group(['namespace' => 'Payment', 'prefix' => 'payment'], function () {
	Route::get('step1', 'StepOneController@index');

	Route::get('step2/{product?}', 'StepTwoContoller@index')->name('payment-provide-personal-data');
	Route::post('step2', 'StepTwoContoller@handle');

	Route::get('step3', 'StepThreeController@index');
	Route::post('step3', 'StepThreeController@handle');
});

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
