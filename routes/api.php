<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\LoginRegisterController;
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

Route::post('login-user', [LoginRegisterController::class, 'authenticate'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('getData/', [DataController::class, 'get'])->name('get-data');
  Route::get('filter/{param}', [DataController::class, 'filter'])->name('filter');
  Route::get('logout-user', [LoginRegisterController::class, 'logout']);
});
