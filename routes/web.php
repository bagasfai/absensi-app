<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Absen;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/absen', [AbsensiController::class, 'index'])->name('absen.index');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('absen.dashboard');
    Route::get('/', [DashboardController::class, 'dashboard'])->name('absen.welcome');
    Route::post('/absen/store', [AbsensiController::class, 'store'])->name('absen.store');
    Route::get('/absen/create', [AbsensiController::class, 'create'])->name('absen.create');
    Route::get('/absen/keluar', [AbsensiController::class, 'keluar'])->name('absen.keluar');
    Route::post('/absen/create', [AbsensiController::class, 'absenMasuk'])->name('absen.absenMasuk');
    Route::post('/absen/keluar', [AbsensiController::class, 'absenKeluar'])->name('absen.absenKeluar');
    Route::get('/absen/{absen}/edit', [AbsensiController::class, 'edit'])->name(('absen.edit'));
    Route::put('/absen/{absen}/update', [AbsensiController::class, 'update'])->name(('absen.update'));
    Route::delete('/absen/{absen}/delete', [AbsensiController::class, 'delete'])->name(('absen.delete'));

    // edit profiel
    Route::get('/absen/editprofile', [AbsensiController::class, 'editprofile'])->name('editprofile');
    Route::post('/absen/{email}/updateprofile', [AbsensiController::class, 'updateprofile'])->name('updateprofile');

    // histori
    Route::get('/absen/histori', [AbsensiController::class, 'histori'])->name('absen.histori');
    Route::post('/gethistori', [AbsensiController::class, 'gethistori'])->name('absen.gethistori');

    // izin
    Route::get('/absen/izin', [AbsensiController::class, 'izin'])->name('absen.izin');
    Route::get('/absen/izin/buatizin', [AbsensiController::class, 'buatizin'])->name('absen.buatizin');
    Route::post('/absen/izin/storeizin', [AbsensiController::class, 'storeizin'])->name('absen.storeizin');
    Route::get('/absen/izinsakit', [AbsensiController::class, 'izinsakit'])->name('absen.izinsakit');
    Route::post('/absen/izinsakit/action', [AbsensiController::class, 'action'])->name('absen.action');
    Route::get('/absen/izinsakit/{id}/batalapprove', [AbsensiController::class, 'batalapprove'])->name('absen.batalapprove');
    Route::post('/absen/cekizin', [AbsensiController::class, 'cekizin'])->name('absen.cekizin');


    // absensi
    Route::get('/absen/monitor', [AbsensiController::class, 'monitor'])->name('absen.monitor');
    Route::post('/getpresensi', [AbsensiController::class, 'records'])->name('absen.getabsen');
    Route::post('/getrekappresensi', [AbsensiController::class, 'getRekapPresensi'])->name('absen.getRekapAbsen');
    Route::post('/showmap', [AbsensiController::class, 'showmap'])->name('absen.showmap');
    Route::get('/absen/laporan', [AbsensiController::class, 'laporan'])->name('absen.laporan');
    Route::post('/absen/cetaklaporan', [AbsensiController::class, 'cetaklaporan'])->name('absen.cetaklaporan');
    Route::get('/absen/rekap', [AbsensiController::class, 'rekap'])->name('absen.rekap');
    Route::post('/absen/cetakrekap', [AbsensiController::class, 'cetakrekap'])->name('absen.cetakrekap');
    Route::post('/absen/laporan/preview-laporan', [AbsensiController::class, 'previewDataLaporan'])->name('absen.previewlaporan');
    Route::post('/absen/laporan/preview-rekap', [AbsensiController::class, 'previewDataRekap'])->name('absen.previewrekap');
    Route::post('/setperiode', [AbsensiController::class, 'setPeriode'])->name('absen.setPeriode');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth', 'jabatan:SUPERADMIN,TEAM WAGNER,ADMIN')->group(function () {
    // Route::get('/panel', function () {
    //     return view('auth.loginadmin');
    // })->name('loginadmin');

    // Route::get('/panel', [AuthenticatedSessionController::class, 'createadmin']);

    // Route::post('/panel', [AuthenticatedSessionController::class, 'storeadmin'])->name('loginadmin');

    Route::get('/dashboardadmin', [DashboardController::class, 'dashboardadmin'])->name('dashboardadmin');
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::post('/user/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/{email}/update', [UserController::class, 'update'])->name('user.update');
    Route::post('/user/{email}/delete', [UserController::class, 'delete'])->name('user.delete');
});


require __DIR__ . '/auth.php';
