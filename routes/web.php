<?php

use App\Http\Controllers\KusonimeController;
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

Route::get('/', function () {
    return [
        'index' => true,
        'message' => 'Welcome to Kusonime API',
        'author' => 'Azusa ID',
        'param' => '?url=[URL Kusonime]'
    ];
});

Route::get('/kusonime', [KusonimeController::class, 'index']);
