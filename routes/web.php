<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;

// Halaman depan publik dan login tamu ke perusahaan contoh (data simulasi).
Route::get('/', [DemoController::class, 'home'])->name('home');
Route::get('/demo/tamu', [DemoController::class, 'guest'])->middleware('throttle:30,1')->name('demo.tamu');
