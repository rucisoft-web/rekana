<?php

use App\Http\Controllers\DemoController;
use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

// Halaman depan publik dan login tamu ke perusahaan contoh (data simulasi).
Route::get('/', [DemoController::class, 'home'])->name('home');
Route::get('/demo/tamu', [DemoController::class, 'guest'])->middleware('throttle:30,1')->name('demo.tamu');

// Verifikasi publik dari kode QR pada sertifikat dan kartu akses.
Route::get('/verifikasi/sertifikat/{code}', [PrintController::class, 'verifyCertificate'])->middleware('throttle:60,1')->name('verifikasi.sertifikat');
Route::get('/verifikasi/kartu/{code}', [PrintController::class, 'verifyCard'])->middleware('throttle:60,1')->name('verifikasi.kartu');

// Halaman cetak (perlu login sebagai anggota owner).
Route::middleware('auth')->prefix('cetak')->name('cetak.')->group(function () {
    Route::get('sertifikat/{certificate}', [PrintController::class, 'certificate'])->name('sertifikat');
    Route::get('kartu/{card}', [PrintController::class, 'card'])->name('kartu');
    Route::get('kontrak/{contract}/kartu', [PrintController::class, 'contractCards'])->name('kartu-kontrak');
});
