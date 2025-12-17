<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Barang routes
    Route::get('/barang/monitor', [BarangController::class, 'monitor'])->name('barang.monitor');
    Route::get('/barang/search', [BarangController::class, 'search'])->name('barang.search');
    Route::get('/barang/monitor/csv', [BarangController::class, 'exportCsv'])->name('barang.monitor.csv');
    Route::get('/barang/monitor/pdf', [BarangController::class, 'exportPdf'])->name('barang.monitor.pdf');

    Route::get('/barang/import', [BarangController::class, 'importForm'])->name('barang.import.form');
    Route::post('/barang/import/preview', [BarangController::class, 'importPreview'])->name('barang.import.preview');
    Route::post('/barang/import/preview/save', [BarangController::class, 'savePreviewEdits'])->name('barang.import.preview.save');
    Route::post('/barang/import/confirm', [BarangController::class, 'importConfirm'])->name('barang.import.confirm');
    Route::post('/barang/import', [BarangController::class, 'importExcel'])->name('barang.import');
    Route::get('/barang/import/template', [BarangController::class, 'downloadImportTemplate'])->name('barang.import.template');

    Route::resource('barang', BarangController::class)->except(['show']);
    Route::get('barang/{barang}', [BarangController::class, 'show'])->name('barang.show');

    // Barang Masuk
    Route::resource('barang-masuk', BarangMasukController::class);
    Route::get('barang-masuk/{id}/pdf', [BarangMasukController::class, 'pdf'])->name('barang-masuk.pdf');

    // Barang Keluar
    Route::resource('barang-keluar', BarangKeluarController::class);
    Route::get('barang-keluar/{id}/pdf', [BarangKeluarController::class, 'pdf'])->name('barang-keluar.pdf');

    // Activity logs
    Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
});

require __DIR__.'/auth.php';
