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
    Route::get('/periods', [PeriodController::class, 'index'])->name('periods.index');
    Route::get('/periods/create', [PeriodController::class, 'create'])->name('periods.create');
    Route::post('/periods', [PeriodController::class, 'store'])->name('periods.store');
    Route::post('/periods/close', [PeriodController::class, 'closePeriod'])->name('periods.close');
    Route::delete('/periods/{period}', [PeriodController::class, 'destroy'])->name('periods.destroy');

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

    // Rute Kelola Target Porsi Harian (Kalender)
    Route::get('/daily-targets', [App\Http\Controllers\DailyTargetController::class, 'index'])->name('daily-targets.index');
    Route::post('/daily-targets/update', [App\Http\Controllers\DailyTargetController::class, 'updateBulk'])->name('daily-targets.update');
    Route::get('/purchase-plan', [App\Http\Controllers\PurchasePlanController::class, 'index'])->name('purchase-plan.index');

    // Rute untuk Ahli Gizi
    Route::get('/purchase-plan', [App\Http\Controllers\PurchasePlanController::class, 'index'])->name('purchase-plan.index');
    Route::post('/purchase-plan/save', [App\Http\Controllers\PurchasePlanController::class, 'saveOrder'])->name('purchase-plan.save-order');

    // Rute untuk Admin Gudang (VVIP)
    Route::get('/transactions/incoming-check', [App\Http\Controllers\TransactionController::class, 'checkIncomingOrder'])->name('transactions.check-order');
    Route::post('/transactions/incoming-check/store', [App\Http\Controllers\TransactionController::class, 'storeIncomingCheck'])->name('transactions.store-check');

    // Rute Pemakaian Darurat (Barang Keluar)
    Route::get('/transactions/out/create', [App\Http\Controllers\TransactionController::class, 'createOut'])->name('transactions.out-create');
    Route::post('/transactions/out/store', [App\Http\Controllers\TransactionController::class, 'storeOut'])->name('transactions.store-out');

    // Rute Pengembalian Sisa Bahan (Retur Dapur)
    Route::get('/transactions/return/create', [App\Http\Controllers\TransactionController::class, 'createReturn'])->name('transactions.return-create');
    Route::post('/transactions/return/store', [App\Http\Controllers\TransactionController::class, 'storeReturn'])->name('transactions.store-return');

    Route::put('/purchase-plan/update', [App\Http\Controllers\PurchasePlanController::class, 'updateOrder'])->name('purchase-plan.update-order');
    Route::delete('/purchase-plan/delete', [App\Http\Controllers\PurchasePlanController::class, 'destroyOrder'])->name('purchase-plan.destroy-order');

});

require __DIR__.'/auth.php';