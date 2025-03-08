<?php

use App\Http\Controllers\ArusKasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PenitipanBarangController;
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
Route::get('/dashboard/penggilingan/pelanggan', [PelangganController::class, 'index'])->name('penggilingan.pelanggan.index');
Route::get('/dashboard/penggilingan/pelanggan/search', [PelangganController::class, 'search'])->name('penggilingan.pelanggan.search');
Route::get('/dashboard/penggilingan/pelanggan/create', [PelangganController::class, 'create'])->name('penggilingan.pelanggan.create');
Route::post('/dashboard/penggilingan/pelanggan/create/proses', [PelangganController::class, 'store'])->name('penggilingan.pelanggan.create.proses');
Route::get('/dashboard/penggilingan/pelanggan/update/{id}', [PelangganController::class, 'indexUpdate'])->name('penggilingan.pelanggan.update');
Route::put('/dashboard/penggilingan/pelanggan/update/{id}', [PelangganController::class, 'update'])->name('penggilingan.pelanggan.update.proses');
Route::delete('/dashboard/penggilingan/pelanggan/delete/{id}', [PelangganController::class, 'destroy'])->name('penggilingan.pelanggan.destroy');

//tenga kerja list
Route::get('/dashboard/penggilingan/tenaga_kerja', [PegawaiController::class, 'index'])->name('penggilingan.tenaga_kerja.index');
Route::get('/dashboard/penggilingan/tenaga_kerja/search',[PegawaiController::class, 'search'])->name('penggilingan.tenaga_kerja.search');
Route::get('/dashboard/penggilingan/tenaga_kerja/create', [PegawaiController::class, 'create'])->name('penggilingan.tenaga_kerja.create');
Route::post('/dashboard/penggilingan/tenaga_kerja/create/proses', [PegawaiController::class,'store'])->name('penggilingan.tenaga_kerja.create.proses');
Route::get('/dashboard/penggilingan/tenaga_kerja/update/{id}', [PegawaiController::class, 'indexUpdate'])->name('penggilingan.tenaga_kerja.update');
Route::put('/dashboard/penggilingan/tenaga_kerja/update/{id}', [PegawaiController::class, 'update'])->name('penggilingan.tenaga_kerja.update.proses');
Route::delete('/dashboard/penggilingan/tenaga_kerja/delete/{id}', [PegawaiController::class, 'destroy'])->name('penggilingan.tenaga_kerja.destroy');
Route::get('/dashboard/penggilingan/tenaga_kerja/hadir/{id}', [PegawaiController::class, 'hadir'])->name('penggilingan.tenaga_kerja.hadir');
Route::get('/dashboard/penggilingan/tenaga_kerja/tidak_hadir/{id}', [PegawaiController::class, 'tidakHadir'])->name('penggilingan.tenaga_kerja.tidak_hadir');

//penitipan
Route::get('/dashboard/penggilingan/penitipan', [PenitipanBarangController::class, 'index'])->name('penggilingn.penitipan.index');
Route::get('/dashboard/penggilingan/search', [PenitipanBarangController::class, 'search'])->name('penggilingan.penitipan.search');
Route::get('/dashboard/penggilingan/penitipan/create', [PenitipanBarangController::class, 'create'])->name('penggilingan.penitipan.create');
Route::post('/dashboard/penggilingan/penitipan/create/proses', [PenitipanBarangController::class, 'create'])->name('penggilingan.penitipan.create.proses');
Route::get('/dashboard/penggilingan/penitipan/update/{id}', [PenitipanBarangController::class, 'indexUpdate'])->name('penggilingan.penitipan.update');
Route::put('/dashboard/penggilingan/penitipan/update/{id}', [PenitipanBarangController::class, 'update'])->name('penggilingan.penitipan.update.proses');
Route::delete('/dashboard/penggilingan/penitipan/delete/{id}', [PenitipanBarangController::class, 'destroy'])->name('penggilingan.penitipan.destroy');

Route::get('/dashboard/penggilingan/mesin', [ViewController::class, 'viewPenggilinganMesin']);
Route::get('/dashboard/penggilingan/perbaikan', [ViewController::class, 'viewPenggilinganPerbaikan']);

//tampilan profil pengguna
Route::get('/dashboard/profil', [ViewController::class, 'viewProfile']);
Route::get('/dashboard/profil/pengaturan', [ViewController::class, 'viewProfilePengaturan']);




