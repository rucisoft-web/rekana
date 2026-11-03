<?php

use App\Http\Controllers\DemoController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\InstitutionMember;
use Illuminate\Support\Facades\Route;

// Halaman depan publik, halaman untuk pimpinan perguruan tinggi, dan login tamu ke perguruan tinggi contoh.
Route::get('/', [DemoController::class, 'home'])->name('home');
Route::get('/untuk-pimpinan', [DemoController::class, 'pimpinan'])->name('pimpinan');
Route::get('/workshop', [DemoController::class, 'workshop'])->name('workshop');
Route::get('/demo/tamu', [DemoController::class, 'guest'])->middleware('throttle:30,1')->name('demo.tamu');

// Portal Bukti Publik: tautan yang ditempel di LED dan dibuka asesor tanpa login.
Route::get('/bukti/{institution:slug}', [PublicController::class, 'evidenceIndex'])->name('bukti.index');
Route::get('/bukti/{institution:slug}/{slug}', [PublicController::class, 'evidence'])->name('bukti.show');

// Rujukan resmi indikator untuk panel Bantuan.
Route::get('/bantuan/indikator/{number}', [PublicController::class, 'helpIndicator'])->whereNumber('number')->name('bantuan.indikator');

// Kuesioner kepuasan publik.
Route::get('/survei/{token}', [PublicController::class, 'survey'])->name('survei.show');
Route::post('/survei/{token}', [PublicController::class, 'submitSurvey'])->middleware('throttle:public-survey')->name('survei.submit');

Route::middleware('auth')->group(function () {
    Route::get('/dokumen/unduh/{version}', [PublicController::class, 'download'])->middleware('signed')->name('dokumen.unduh');

    Route::prefix('laporan/{institution}')->middleware(InstitutionMember::class)->name('laporan.')->group(function () {
        Route::get('lkpt.xlsx', [ReportController::class, 'lkpt'])->name('lkpt');
        Route::get('kertas-kerja.xlsx', [ReportController::class, 'workpapers'])->name('kertas-kerja');
        Route::get('bukti-publik.xlsx', [ReportController::class, 'publicEvidence'])->name('bukti-publik');
        Route::get('led.docx', [ReportController::class, 'ledDocx'])->name('led-docx');
        Route::get('led', [ReportController::class, 'ledPrint'])->name('led');
        Route::get('syarat-perlu', [ReportController::class, 'prerequisites'])->name('syarat-perlu');
        Route::get('mingguan', [ReportController::class, 'weekly'])->name('mingguan');
        Route::get('log-audit.csv', [ReportController::class, 'audit'])->name('audit');
    });
});
