<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;

// Redirect homepage ke login admin
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Admin Auth (login/logout)
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
// Alias for packages/middleware expecting a `login` route name
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Semua route yang butuh login admin
Route::middleware('auth:admin')->group(function () {

    // Dashboard admin setelah login
    Route::get('/admin/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');

    // Dashboard utama (Ringkasan)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{flower}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
    Route::put('/inventory/{flower}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{flower}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    // Produksi
    Route::get('/productions', [ProductionController::class, 'index'])->name('productions.index');
    Route::get('/productions/create', [ProductionController::class, 'create'])->name('productions.create');
    Route::get('/productions/edit', [ProductionController::class, 'edit'])->name('productions.edit');
    Route::post('/productions', [ProductionController::class, 'store'])->name('productions.store');
    Route::post('/productions/update', [ProductionController::class, 'update'])->name('productions.update');
    Route::delete('/productions/{production}', [ProductionController::class, 'destroy'])->name('productions.destroy');

    // Pesanan
    Route::get('/pesanan', [OrderController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/create', [OrderController::class, 'create'])->name('pesanan.create');
    Route::post('/pesanan', [OrderController::class, 'store'])->name('pesanan.store');
    Route::get('/pesanan/{pesanan}', [OrderController::class, 'show'])->name('pesanan.show');
    Route::get('/pesanan/{pesanan}/edit', [OrderController::class, 'edit'])->name('pesanan.edit');
    Route::put('/pesanan/{pesanan}', [OrderController::class, 'update'])->name('pesanan.update');
    Route::delete('/pesanan/{pesanan}', [OrderController::class, 'destroy'])->name('pesanan.destroy');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [App\Http\Controllers\LaporanController::class, 'export'])
    ->name('laporan.export');

});
