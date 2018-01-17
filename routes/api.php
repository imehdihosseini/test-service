<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([],function () {
    Route::get('/transactionservice/transaction', 'TransactionController@index');
    Route::get('/transactionservice/transaction/{id}', 'TransactionController@show');
    //Route::post('/transactionservice/transaction', 'TransactionController@store');
    Route::put('/transactionservice/transaction/{id}', 'TransactionController@update');
    Route::delete('/transactionservice/transaction/{id}', 'TransactionController@destroy');
    Route::get('/transactionservice/sum/{id}', 'TransactionController@sum');
    Route::get('/transactionservice/types/{id}', 'TransactionController@types');
});
