<?php

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

    // Finance => Transaction Type
    Route::apiResource('transaction-type', 'TransactionTypeController');

    // Finance => Transaction
    Route::apiResource('transaction', 'TransactionController')->except('show');
    Route::prefix('transaction')->group(function () {
        Route::patch('done/{id}', 'TransactionController@done')->name('transaction.done');
        Route::get('sum_amount', 'TransactionController@sum_amount')->name('transaction.sum.amount');
    });
});