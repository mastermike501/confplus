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

Route::get('/', function () {
    return view('index');
});

Route::group(['prefix' => 'api'], function() {

    Route::resource('v1', 'ConfplusControllerV1',
        ['only' => ['store']]
    );

    // since we will be using this just for CRUD, we won't need create and edit
    // Angular will handle both of those forms
    // this ensures that a user can't access api/create or api/edit when there's nothing there

    // Route::resource('users', 'UserController',
    // 	['only' => ['store', 'show']]
    // );
    //
    // Route::resource('papers', 'PaperController',
    // 	['only' => ['store', 'show']]
    // );
    //
    // Route::resource('events', 'EventController',
    //     ['only' => ['store', 'show']]
    // );
    //
    // Route::resource('session', 'SessionController',
    //     ['only' => ['store', 'show']]
    // );
    //
    // Route::resource('venue', 'VenueController',
    //     ['only' => ['store', 'show']]
    // );
    //
    // Route::resource('resource', 'ResourceController',
    //     ['only' => ['store', 'show']]
    // );

});
