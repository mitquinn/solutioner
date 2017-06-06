<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@show');



// Route::get('/test', function () {
//     print_r("expression");
// });
//
Route::get('/home', 'HomeController@show');

//
//Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//Route::get('/api/test', function() {
//    return ['name' => 'Mitchell'];
//});

Route::group([
    'prefix' => 'api',
    'middleware' => ['auth:api']
], function () {
    Route::resource('solutions', 'SolutionController', ['except' => [
        'create', 'edit'
    ]]);
    Route::get('solutions/{id}/tags', 'SolutionController@tags');
    Route::post('solutions/{id}/tags', 'SolutionController@attachTag');
    Route::delete('solutions/{solution_id}/tags/{tag_id}', 'SolutionController@removeTag');
    Route::get('tags', 'TagController@index');
    Route::post('tags', 'TagController@store');
    Route::get('tags/{id}', 'TagController@show');
});
