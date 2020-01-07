<?php

use Illuminate\Http\Request;

// Index API
Route::get('/', function () {
    return response()->json(['code' => 200, 'message' => 'Tiny Studio App API']);
});

// Passport Auth
Route::post('login', 'AuthController@login')->name('login');
Route::get('unauthorized', 'AuthController@unauthorized')->name('unauthorized');
Route::post('logout', 'AuthController@logout');

Route::apiResource('transaction-type', 'TransactionTypeController');
Route::apiResource('transaction', 'TransactionController');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
    
// });