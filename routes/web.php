<?php

use App\Http\Controllers\ArusKasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UtangPiutangController;

//tampilan login log out
Route::get('/', [ViewController::class, 'viewLogin']);
Route::get('/login',  [ViewController::class, 'viewLogin']);
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

//tampilan dashboard
Route::middleware('auth')->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'viewDashboard']);
});

//tampilan keuangan kas
Route::get('/dashboard/keuangan/kas', [ArusKasController::class, 'index'])->name('keuangan.kas.index');
Route::get('/dashboard/keuangan/kas/search', [ArusKasController::class, 'search'])->name('keuangan.kas.search');
Route::get('/dashboard/keuangan/kas/create', [ArusKasController::class, 'indexCreate'])->name('keuangan.kas.create');
Route::post('/dashboard/keuangan/kas/create/proses', [ArusKasController::class, 'create'])->name('keuangan.kas.create.proses');
Route::get('/dashboard/keuangan/kas/update/{id}', [ArusKasController::class, 'indexUpdate'])->name('keuangan.kas.update');
Route::put('/dashboard/keuangan/kas/update/{id}', [ArusKasController::class, 'update'])->name('keuangan.kas.update.proses');
Route::delete('/dashboard/keuangan/kas/delete/{id}', [ArusKasController::class, 'destroy'])->name('keuangan.kas.destroy');

//tampilan keuangan Utang
Route::get('/dashboard/keuangan/utang', [UtangPiutangController::class, 'indexUtang'])->name('keuangan.utang.index');
Route::get('/dashboard/keuangan/utang/search', [UtangPiutangController::class, 'searchUtang'])->name('keuangan.utang.search');
Route::get('/dashboard/keuangan/utang/create', [UtangPiutangController::class, 'createIndexUtang'])->name('keuangan.utang.create');
Route::get('/dashboard/keuangan/utang/create/cek-pelanggan', [UtangPiutangController::class,'checkPelanggan'])->name('keuangan.utang.create.cek-pelanggan');
Route::post('/dashboard/keuangan/utang/create/proses', [UtangPiutangController::class, 'create'])->name('keuangan.utang.create.proses');

//tampilan laporan
Route::get('/dashboard/laporan/arus_kas', [ArusKasController::class, 'index']);
Route::get('/dashboard/laporan/utang_piutang', [ViewController::class, 'viewLaporanUtangPiutang']);
Route::get('/dashboard/laporan/laba_rugi', [ViewController::class, 'viewLaporanLabaRugi']);
Route::get('/dashboard/laporan/stock', [ViewController::class, 'viewLaporanStock']);

//tampilan penggilingan
Route::get('/dashboard/penggilingan/pelanggan', [ViewController::class, 'viewPenggilinganPelanggan']);
Route::get('/dashboard/penggilingan/tenaga_kerja', [ViewController::class, 'viewPenggilinganTenagaKerja']);
Route::get('/dashboard/penggilingan/penitipan', [ViewController::class, 'viewPenggilinganPenitipan']);
Route::get('/dashboard/penggilingan/mesin', [ViewController::class, 'viewPenggilinganMesin']);
Route::get('/dashboard/penggilingan/perbaikan', [ViewController::class, 'viewPenggilinganPerbaikan']);

//tampilan profil pengguna
Route::get('/dashboard/profil', [ViewController::class, 'viewProfile']);
Route::get('/dashboard/profil/pengaturan', [ViewController::class, 'viewProfilePengaturan']);




