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
        $role = Auth::user()->role;

        // Role-specific redirects
        $roleRedirects = [
            'penerimaan' => 'barang-masuk.index',
            'pengeluaran' => 'barang-keluar.index',
        ];

        // Check if role has specific redirect
        if (array_key_exists($role, $roleRedirects)) {
            $route = $roleRedirects[$role];
            // Check if user has access to this menu
            $menu = \App\Models\Menu::where('route', $route)->first();
            if ($menu && $menu->hasRole($role)) {
                return redirect()->route($route);
            }
        }

        // Default redirect to dashboard if user has access
        $dashboardMenu = \App\Models\Menu::where('route', 'dashboard')->first();
        if ($dashboardMenu && $dashboardMenu->hasRole($role)) {
            return redirect()->route('dashboard');
        }

        // Fallback: redirect to first accessible menu or show error
        return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke menu manapun.');
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
});

Route::middleware(['auth', 'menu'])->group(function () {
    // Barang routes (admin only)
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

    // QR Scan (admin only)
    Route::middleware('menu')->group(function () {
        Route::get('/scan-qr', [BarangController::class, 'scanQr'])->name('barang.scan-qr');
        Route::post('/scan-qr/process', [BarangController::class, 'processQr'])->name('barang.process-qr');
        Route::get('/barang/{barang}/qr', [BarangController::class, 'generateQr'])->name('barang.qr');
    });

    // Activity logs (admin only)
    Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // User management
    Route::resource('users', \App\Http\Controllers\UserController::class);

    // Menu management (admin only)
    Route::resource('menus', \App\Http\Controllers\MenuController::class);

    // Role management (admin only)
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
});

// Barang Masuk (penerimaan)
Route::middleware(['auth', 'menu'])->group(function () {
    Route::resource('barang-masuk', BarangMasukController::class);
    Route::get('barang-masuk/{id}/pdf', [BarangMasukController::class, 'pdf'])->name('barang-masuk.pdf');
});

// Barang Keluar (pengeluaran)
Route::middleware(['auth', 'menu'])->group(function () {
    Route::resource('barang-keluar', BarangKeluarController::class);
    Route::get('barang-keluar/{id}/pdf', [BarangKeluarController::class, 'pdf'])->name('barang-keluar.pdf');
});

require __DIR__.'/auth.php';
