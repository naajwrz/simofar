<?php

use App\Http\Controllers\PermintaanResepController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ApotekerController as AdminApotekerController;
use App\Http\Controllers\Admin\KeuanganController as AdminKeuanganController;
use App\Http\Controllers\Admin\PelangganController as AdminPelangganController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\Apoteker\ObatController;
use App\Http\Controllers\Apoteker\ResepController;
use App\Http\Controllers\Apoteker\PengadaanController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pelanggan;
use App\Models\Role;
use App\Http\Controllers\CheckoutController;

// Redirect Halaman Utama ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/checkout', [CheckoutController::class, 'store'])
    ->name('checkout.store');

Route::get('/checkout/pembayaran', [CheckoutController::class, 'show'])
    ->name('checkout.show');

Route::post('/checkout/konfirmasi', [CheckoutController::class, 'confirm'])
    ->name('checkout.confirm');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Katalog
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');

// Route Darurat Reset Password
Route::get('/fix-login', function () {
    $user = User::where('email', 'apoteker@simofar.com')->first();
    
    if (!$user) {
        $user = new User();
        $user->email = 'apoteker@simofar.com';
        $user->nama_staf = 'Najwa Apoteker';
        $user->id_role = 1; // ID Role Apoteker
        $user->status = 'aktif';
    }
    
    $user->password = Hash::make('password123');
    $user->save();

    return "Password akun apoteker@simofar.com BERHASIL direset menjadi 'password123'. Silakan coba login lagi!";
});

// Halaman register pelanggan (duplikat dibersihkan jadi 1 saja)
Route::get('/register', [AuthController::class, 'showRegisterForm'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');

// Group Route khusus Apoteker
Route::middleware(['auth'])->prefix('apoteker')->name('apoteker.')->group(function () {
    Route::get('/dashboard', function () {
        if (!auth()->user()->isApoteker()) {
            return redirect('/login')->withErrors(['email' => 'Akses khusus untuk Apoteker.']);
        }
        return view('apoteker.dashboard');
    })->name('dashboard');

    // Modul Stok Obat & Kategori
    Route::get('/obat', [ObatController::class, 'index'])->name('obat.index');
    Route::post('/obat', [ObatController::class, 'storeObat'])->name('obat.store');
    Route::post('/kategori', [ObatController::class, 'storeKategori'])->name('kategori.store');
    Route::put('/obat/{id}', [ObatController::class, 'update'])->name('obat.update');
    Route::delete('/obat/{id}', [ObatController::class, 'destroy'])->name('obat.destroy');

    // Modul Resep
    Route::get('/resep', [ResepController::class, 'index'])->name('resep.index');
    Route::get('/resep/pasien', [ResepController::class, 'patients'])->name('resep.pasien');
    Route::post('/resep', [ResepController::class, 'store'])->name('resep.store');
    Route::post('/resep/{id}/verifikasi', [ResepController::class, 'verifikasi'])->name('resep.verifikasi');
    Route::post('/resep/{id}/acc', [ResepController::class, 'acc'])->name('resep.acc');
    Route::post('/resep/{id}/tolak-permintaan', [ResepController::class, 'tolakPermintaan'])->name('resep.tolakPermintaan');

    // Modul Pengadaan
    Route::get('/pengadaan', [PengadaanController::class, 'index'])
        ->name('pengadaan.index');

    Route::post('/pengadaan', [PengadaanController::class, 'store'])
        ->name('pengadaan.store');

    // Tambah Supplier
    Route::post('/supplier', [PengadaanController::class, 'storeSupplier'])
        ->name('supplier.store');
});

// Pelanggan: permintaan obat keras
Route::middleware('auth')->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/permintaan', [PermintaanResepController::class, 'index'])->name('permintaan.index');
    Route::post('/permintaan', [PermintaanResepController::class, 'store'])->name('permintaan.store');
    Route::get('/permintaan/{id}/pembayaran', [CheckoutController::class, 'showPrescriptionPayment'])
        ->name('permintaan.pembayaran');
    Route::post('/permintaan/{id}/pembayaran', [CheckoutController::class, 'confirmPrescriptionPayment'])
        ->name('permintaan.pembayaran.confirm');
});

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/diagram', [AdminDashboardController::class, 'diagram'])->name('diagram');
    Route::get('/keuangan', [AdminKeuanganController::class, 'index'])->name('keuangan');
    Route::post('/keuangan', [AdminKeuanganController::class, 'save'])->name('keuangan.save');

    Route::get('/apoteker', [AdminApotekerController::class, 'index'])->name('apoteker.index');
    Route::post('/apoteker', [AdminApotekerController::class, 'store'])->name('apoteker.store');
    Route::put('/apoteker/{id}', [AdminApotekerController::class, 'update'])->name('apoteker.update');

    Route::get('/pelanggan', [AdminPelangganController::class, 'index'])->name('pelanggan.index');
    Route::put('/pelanggan/{id}', [AdminPelangganController::class, 'update'])->name('pelanggan.update');
});