<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ArusKasController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\MesinController;
use App\Http\Controllers\UtangPiutangController;
use App\Http\Controllers\PenitipanBarangController;
use App\Models\Aset;

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
Route::get('/dashboard/keuangan/utang/create/cek-auto', [UtangPiutangController::class,'cekAuto'])->name('keuangan.utang.create.cek-auto');
Route::get('/dashboard/keuangan/utang/create/cek-pelanggan', [UtangPiutangController::class,'checkPelanggan'])->name('keuangan.utang.create.cek-pelanggan');
Route::post('/dashboard/keuangan/utang/create/proses', [UtangPiutangController::class, 'createUtang'])->name('keuangan.utang.create.proses');

//tampilan laporan
Route::get('/dashboard/laporan/arus_kas', [ArusKasController::class, 'index']);
Route::get('/dashboard/laporan/utang_piutang', [ViewController::class, 'viewLaporanUtangPiutang']);
Route::get('/dashboard/laporan/laba_rugi', [ViewController::class, 'viewLaporanLabaRugi']);
Route::get('/dashboard/laporan/stock', [ViewController::class, 'viewLaporanStock']);

//tampilan penggilingan
//tampilan pelanggan
Route::resource('/penggilingan/pelanggan', PelangganController::class)->except(['show']);
Route::get('/pelanggan/search', [PelangganController::class, 'search'])->name('penggilingan.pelanggan.search');

//tenga kerja list
Route::resource('/penggilingan/tenaga_kerja', PegawaiController::class)->except(['show']);
Route::get('/penggilingan/tenaga_kerja/search',[PegawaiController::class, 'search'])->name('tenaga_kerja.search');
Route::get('/penggilingan/tenaga_kerja/hadir/{id}', [PegawaiController::class, 'hadir'])->name('penggilingan.tenaga_kerja.hadir');
Route::get('/penggilingan/tenaga_kerja/tidak_hadir/{id}', [PegawaiController::class, 'tidakHadir'])->name('penggilingan.tenaga_kerja.tidak_hadir');

//penitipan
Route::resource('/penggilingan/penitipan', PenitipanBarangController::class)->except(['show']);
Route::get('/dashboard/penggilingan/penitipan/search', [PenitipanBarangController::class, 'search'])->name('penggilingan.penitipan.search');
Route::get('/dashboard/penggilingan/penitipan/create/cek-pelanggan', [PenitipanBarangController::class, 'cekPelanggan'])->name('penggilingan.penitipan.create.cek-pelanggan');
Route::get('/dashboard/penggilingan/penitipan/create/cek-auto', [PenitipanBarangController::class, 'cekAuto'])->name('penggilingan.penitipan.create.cek-auto');

//Aset
Route::resource('/penggilingan/aset', AsetController::class)->except(['show']);
Route::get('/dashboard/penggilingan/aset/search', [AsetController::class,'search'])->name('penggilingan.aset.search');

//stock
Route::resource('/penggilingan/stock', StockController::class)->except(['show']);
Route::get('/dashboard/penggilingan/stock/search', [StockController::class,'search'])->name('penggilingan.stock.search');
Route::get('/dashboard/penggilingan/stock/create/cek', [StockController::class, 'cek'])->name('penggilingan.stock.create.cek');
Route::get('/dashboard/penggilingan/stock/create/cek-auto', [StockController::class, 'cekAuto'])->name('penggilingan.stock.create.cek-auto');

//mesin
Route::resource('/penggilingan/mesin', MesinController::class)->except(['show']);
Route::get('/dashboard/penggilingan/mesin/search', [MesinController::class,'search'])->name('penggilingan.mesin.search');

//tampilan penggilingan
Route::get('/dashboard/penggilingan/perbaikan', [ViewController::class, 'viewPenggilinganPerbaikan']);

//tampilan profil pengguna
Route::get('/dashboard/profil', [ViewController::class, 'viewProfile']);
Route::get('/dashboard/profil/pengaturan', [ViewController::class, 'viewProfilePengaturan']);




