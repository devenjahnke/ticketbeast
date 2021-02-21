<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ConcertOrdersController;
use App\Http\Controllers\ConcertsController;
use App\Http\Controllers\Backstage\ConcertsController as Backstage_ConcertsController;
use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/concerts/{id}', [ConcertsController::class, 'show'])->name('concerts.show');

Route::post('/concerts/{id}/orders', [ConcertOrdersController::class, 'store']);

Route::get('/orders/{confirmationNumber}', [OrdersController::class, 'show']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('auth.show-login');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'auth', 'prefix' => 'backstage'], function () {
    Route::get('/concerts/new', [Backstage_ConcertsController::class, 'create']);
    Route::post('/concerts', [Backstage_ConcertsController::class, 'store']);
});
