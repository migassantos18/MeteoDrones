<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

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


//Auth::routes();

Auth::routes([
    'reset' => false,
    'verify' => false,
    'confirm' => false,
  ]);

Route::get('/', [Controller::class, 'index'])->name('dashboard');
Route::get('/get-configs', [Controller::class, 'getConfigs']);
Route::get('/historico', [Controller::class, 'history'])->name('history');
Route::get('/historico/{id}', [Controller::class, 'historyDashboard'])->name('historyDashboard');
Route::get('/sobre', [Controller::class, 'about'])->name('about');

Route::middleware('auth')->group(function () {
    Route::get('/configuracoes', [Controller::class, 'configs'])->name('configs');
    Route::post('/configuracoes', [Controller::class, 'saveConfigs'])->name('save-configs');
});