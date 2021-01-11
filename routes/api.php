<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post("/user", [UserController::class, 'store']);
Route::post("/user/login", [UserController::class, 'login']);
Route::get("/user/{id}", [UserController::class, 'show']);
Route::post("/transaction/{id}", [TransactionController::class, 'createTransaction']);
Route::get("/banking-accounts/{id}", [UserController::class, 'getInfoBal']);
Route::get("/transactions/{id}", [TransactionController::class, 'index']);
Route::get("/transaction/{id}", [TransactionController::class, 'show']);
