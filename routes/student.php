<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| student Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//==============================Translate all pages============================
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'auth:student']
    ], function () {

    //==============================dashboard============================
    Route::get('/student/dashboard', function () {
        return view('pages.Students.dashboard');

    });
    Route::get('/student/today-quizzes','Students\StudentController@today_quizzes');
    Route::get('/student/previous-quizes','Students\StudentController@previous_quizes');
    Route::get('/student/perform-exam/{id}','Students\StudentController@perform_exam');
    Route::post('/student/end-exam','Students\StudentController@end_exam');
    Route::get('/student/eye-test/{quiz_id}','Students\StudentController@eyeTest');
    //face
    Route::get('api/abort-test/{type?}', 'Students\StudentController@abort');
    Route::get('api/eye-test-completed', 'Students\StudentController@eyeTestCompleted');
    Route::get('profile', 'Students\ProfileSController@index')->name('profile.show');
    Route::post('profile/{id}', 'Students\ProfileSController@update')->name('profile.update');

});
