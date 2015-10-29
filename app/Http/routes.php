<?php

/*
|--------------------------------------------------------------------------
| API routes
|--------------------------------------------------------------------------
|
| Group of routs for first version of our API.
|
*/

Route::group(['domain' => 'api.v1.example.local', 'namespace' => 'API\v1'], function()
{

    Route::get('/', function()
    {
        echo "API called!";
    });

    Route::get('/user/list/{start?}', ['uses' => 'User\UserController@getList'])->where('start', '[0-9]+');

    Route::resource('user', 'User\UserController', ['except' => ['create', 'edit', 'store', 'destroy']]);
    Route::post('/signup', ['uses' => 'Auth\AuthController@postRegister']);
    Route::post('/signin', ['uses' => 'Auth\AuthController@registration']);

});

/**
 * Routes for our front-end
 */
Route::get('/', function () {
    return view('welcome');
});
