<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

//tampilan login log out
Route::get('/', [ViewController::class, 'viewLogin']);
Route::get('/login',  [ViewController::class, 'viewLogin']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

//tampilan dashboard
Route::get('/dashboard', [DashboardController::class, 'viewDashboard']);

//tampilan keuangan
Route::get('/dashboard/keuangan/kas', [ViewController::class, 'viewKeuanganKas']);
Route::get('/dashboard/keuangan/utang', [ViewController::class, 'viewKeuanganUtang']);
Route::get('/dashboard/keuangan/piutang', [ViewController::class, 'viewKeuanganPiutang']);

//tampilan laporan
Route::get('/dashboard/laporan/arus_kas', [ViewController::class, 'viewLaporanArusKas']);
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

Route::post('/login', [LoginController::class, 'Login']);




