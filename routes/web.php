<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DailyMenuController; // <-- Tambahan agar rapi
use App\Http\Controllers\UsageRecapController;

Route::get('/', function () {
    return view('welcome');
});

// --- DASHBOARD ---
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- SEMUA ROUTE YANG BUTUH LOGIN (AUTH) ---
Route::middleware('auth')->group(function () {

    // 1. PROFILE
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // 2. BARANG MASUK & TRANSAKSI
    Route::controller(TransactionController::class)->prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/recap', 'recap')->name('recap'); // Custom ditaruh paling atas
        Route::get('/in', 'indexIn')->name('in');
        Route::get('/in/create', 'createIn')->name('createIn');
        Route::post('/in', 'storeIn')->name('storeIn');
        Route::get('/in/{id}/edit', 'editIn')->name('editIn');
        Route::put('/in/{id}', 'updateIn')->name('updateIn');
        Route::delete('/in/{id}', 'destroyIn')->name('destroyIn');
    });

    // 3. PERIODE
    Route::post('/periods/reset', [PeriodController::class, 'resetPeriod'])->name('periods.reset');
    Route::get('/periods/create', [PeriodController::class, 'create'])->name('periods.create');
    Route::post('/periods/store', [PeriodController::class, 'store'])->name('periods.store');

    // 4. PENERIMA MANFAAT (BENEFICIARIES)
    Route::get('/beneficiaries/create-posyandu', [BeneficiaryController::class, 'createPosyandu'])->name('beneficiaries.create-posyandu');
    Route::resource('beneficiaries', BeneficiaryController::class); 
    // Catatan: Route manual (index & store) dihapus karena sudah otomatis ditangani oleh Route::resource

    // 5. MASTER BAHAN (ITEMS)
    Route::resource('items', ItemController::class);
    // Catatan: Route manual (create & store) dihapus karena sudah otomatis ditangani oleh Route::resource

    // 6. MASTER MENU & RESEP
    Route::post('/menus/{menu}/ingredients', [MenuController::class, 'addIngredient'])->name('menus.ingredients.add');
    Route::delete('/menus/{menu}/ingredients/{item}', [MenuController::class, 'removeIngredient'])->name('menus.ingredients.remove');
    Route::get('/menus/{menu}/plan', [MenuController::class, 'plan'])->name('menus.plan');
    Route::resource('menus', MenuController::class);

    // 7. JADWAL MENU (KALENDER)
    Route::post('/daily-menus/{dailyMenu}/execute', [DailyMenuController::class, 'execute'])->name('daily-menus.execute');
    Route::resource('daily-menus', DailyMenuController::class)->only(['index', 'store', 'destroy']);

    // 8. REKAP PENGGUNAAN
    Route::get('/usage-recaps', [UsageRecapController::class, 'index'])->name('usage-recaps.index');

    Route::get('/periods/create', [App\Http\Controllers\PeriodController::class, 'create'])->name('periods.create');
    Route::post('/periods', [App\Http\Controllers\PeriodController::class, 'store'])->name('periods.store');

    Route::get('/daily-targets', [App\Http\Controllers\DailyTargetController::class, 'index'])->name('daily-targets.index');
    Route::post('/daily-targets/update', [App\Http\Controllers\DailyTargetController::class, 'update'])->name('daily-targets.update');

});

require __DIR__.'/auth.php';