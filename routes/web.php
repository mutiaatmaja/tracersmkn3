<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/template', function () {
    return view('template');
});
// Beta Testing Routes
Route::get('/beta/cetak/dompdf', [App\Http\Controllers\BetaController::class, 'betaCetakDompdf']);Route::get('/beta/cetak/cloudflare', [App\Http\Controllers\BetaController::class, 'betaCetakCloudflare']);
