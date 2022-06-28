<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/transactions', [TransactionController::class, 'listAll']);

Route::group(['prefix' => '/transaction'], function () {
    Route::post('/create', [TransactionController::class, 'create']);
    Route::get('/{id}', [TransactionController::class, 'getById']);
    Route::delete('/{id}', [TransactionController::class, 'delete']);
});
