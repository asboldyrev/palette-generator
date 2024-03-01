<?php

use App\Http\Controllers\ImageController;
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
    $router->get('/', 'create')->name('images.create');
    $router->post('store', 'store')->name('images.store');
});

Route::controller(ImageController::class)->prefix('result')->group(function (Router $router) {
    $router->get('/', 'list')->name('images.list');
    $router->get('{id}/show', 'show')->name('images.show');
});
