<?php

use App\Http\Controllers\SiteController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(SiteController::class)->group(function (Router $router) {
    $router->get('/', 'index')->name('index');
    $router->get('result/{id}/{version?}', 'result')->name('result');
    $router->post('/', 'store')->name('store');
});
