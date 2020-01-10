<?php

use Illuminate\Http\Request;

// Index API
Route::get('/', function () {
    return response()->json(['code' => 200, 'message' => 'Tiny App API']);
});

// Passport Auth
Route::post('login', 'AuthController@login')->name('login');
Route::get('unauthorized', 'AuthController@unauthorized')->name('unauthorized');
Route::post('logout', 'AuthController@logout');


Route::group(['middleware' => ['auth:api']], function() {
    Route::get('is_login', function () {
        return response()->json([
            'code' => 200,
            'message' => 'Logged in',
        ]);
    });

    Route::apiResource('transaction-type', 'TransactionTypeController');

    Route::prefix('transaction')->group(function () {
        Route::apiResource('', 'TransactionController');
        Route::patch('done/{id}', 'TransactionController@done')->name('transaction.done');
    });
});