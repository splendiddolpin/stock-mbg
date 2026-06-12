<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\{
    ProfileController, DashboardController, TransactionController, ItemController,
    PeriodController, BeneficiaryController, MenuController, DailyMenuController,
    UsageRecapController, DailyTargetController, PurchasePlanController, 
    MenuCatalogController, StaffCashController, MenuRequestController, ArchiveController
};

// 1. RUTE PUBLIK (Tanpa Login)
Route::get('/request-menu', [MenuRequestController::class, 'createPublic'])->name('request-menu.create');
Route::post('/request-menu', [MenuRequestController::class, 'storePublic'])
    ->middleware('throttle:5,1')
    ->name('request-menu.store');

Route::get('/', function () { return view('welcome'); });

// 2. RUTE AUTHENTICATED
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PROFILE
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // PERIODE & ARSIP (PENTING: Export & Close di atas resource agar tidak konflik)
    Route::controller(PeriodController::class)->prefix('periods')->name('periods.')->group(function () {
        Route::post('/close', 'closePeriod')->name('close');
        Route::get('/{id}/export-excel', 'exportExcel')->name('export-excel');
    });
    Route::resource('periods', PeriodController::class);

    // BARANG & TRANSAKSI (Gudang)
    Route::resource('items', ItemController::class);
    Route::controller(TransactionController::class)->prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/recap', 'recap')->name('recap');
        Route::get('/in', 'indexIn')->name('in');
       Route::get('/in/create', 'createIn')->name('createIn');
        Route::post('/in', 'storeIn')->name('storeIn');
        Route::get('/incoming-check', 'checkIncomingOrder')->name('check-order');
        Route::post('/incoming-check/store', 'storeIncomingCheck')->name('store-check');
        Route::get('/out/create', 'createOut')->name('out-create');
        Route::post('/out/store', 'storeOut')->name('store-out');
        Route::get('/return/create', 'createReturn')->name('return-create');
        Route::post('/return/store', 'storeReturn')->name('store-return');
        
    });
    Route::get('/usage-recaps', [UsageRecapController::class, 'index'])->name('usage-recaps.index');

    // PENERIMA MANFAAT
    Route::get('/beneficiaries/create-posyandu', [BeneficiaryController::class, 'createPosyandu'])->name('beneficiaries.create-posyandu');
    Route::resource('beneficiaries', BeneficiaryController::class);
    Route::resource('beneficiaries', BeneficiaryController::class);

    // MENU & RESEP
    Route::resource('menus', MenuController::class);
    Route::controller(MenuController::class)->prefix('menus')->name('menus.')->group(function () {
        Route::post('/{menu}/ingredients', 'addIngredient')->name('ingredients.add');
        Route::delete('/{menu}/ingredients/{item}', 'removeIngredient')->name('ingredients.remove');
    });

    // JADWAL MENU (Daily Menus)
    Route::delete('/daily-menus/destroy-all', [DailyMenuController::class, 'destroyAll'])->name('daily-menus.destroy-all');
    Route::resource('daily-menus', DailyMenuController::class)->except(['create', 'edit', 'update']);
    Route::post('/daily-menus/{dailyMenu}/execute', [DailyMenuController::class, 'execute'])->name('daily-menus.execute');

    // TARGET, PURCHASE PLAN & KATALOG
    Route::controller(DailyTargetController::class)->prefix('daily-targets')->name('daily-targets.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/update', 'updateBulk')->name('update');
    });

    Route::controller(PurchasePlanController::class)->prefix('purchase-plan')->name('purchase-plan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/save', 'saveOrder')->name('save-order');
        Route::put('/update', 'updateOrder')->name('update-order');
        Route::delete('/delete', 'destroyOrder')->name('destroy-order');
    });

    Route::resource('menu-catalogs', MenuCatalogController::class);

    // KEUANGAN
    Route::resource('staff-cash', StaffCashController::class)->only(['index', 'store', 'destroy']);
    Route::post('staff-cash/{id}/pay', [StaffCashController::class, 'payDebt'])->name('staff-cash.pay'); // Rute centang lunas

    // Kelompok Rute Khusus Transaksi Barang Masuk
    Route::get('/transactions/{transaction}/edit-in', [App\Http\Controllers\TransactionController::class, 'editIn'])->name('transactions.editIn');
    Route::put('/transactions/{transaction}/update-in', [App\Http\Controllers\TransactionController::class, 'updateIn'])->name('transactions.updateIn');
    Route::delete('/transactions/{transaction}/destroy-in', [App\Http\Controllers\TransactionController::class, 'destroyIn'])->name('transactions.destroyIn'); // <-- Tambahkan baris ini

    // JALUR DARURAT: BARANG KELUAR MANUAL (PEMAKAIAN EKSTRA)
    Route::get('/transactions/create-out', [App\Http\Controllers\TransactionController::class, 'createOut'])->name('transactions.createOut');
    Route::post('/transactions/store-out', [App\Http\Controllers\TransactionController::class, 'storeOut'])->name('transactions.storeOut');

    // Halaman utama daftar arsip file excel
    Route::get('/archives', [ArchiveController::class, 'index'])->name('archives.index');

    // Proses download filenya
    Route::get('/archives/download/{filename}', [ArchiveController::class, 'download'])->name('archives.download');
});

require __DIR__.'/auth.php';