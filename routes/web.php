<?php

use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\StokController;
use App\Models\Produk;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SesiController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\MemberController;

Route::get('/check-username', function (Request $request) {
    $exists = User::where('name', $request->name)->exists();
    return response()->json(['exists' => $exists]);
});

Route::get('/check-email', function (Request $request) {
    $exists = User::where('email', $request->email)->exists();
    return response()->json(['exists' => $exists]);
});

Route::get('/check-category', function (Request $request) {
    $exists = Category::where('name', $request->name)->exists();
    return response()->json(['exists' => $exists]);
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/welcome', function (){
    return view('welcome');
});

Route::middleware(['guest'])->group(function(){
    Route::get('/', [SesiController::class, 'index'])->name('login');
    Route::post('/login', [SesiController::class, 'login']);
    Route::get('/register', [SesiController::class, 'register']);
    Route::post('/register', [SesiController::class, 'store']);
});

Route::middleware(['auth'])->group(function(){
    Route::get('/logout', [SesiController::class, 'logout']);
    Route::get('/home', function (){
        return view('/home');
    });

    Route::get('/produk', [ProdukController::class, 'index']);
    Route::get('/discount', [DiscountController::class, 'index']);
});

Route::middleware(['auth', 'onlyAdmin'])->group(function () {
    Route::get('/discount-edit={id}', [DiscountController::class, 'edit'])->name('discount.edit');
    Route::put('/discount/{id}', [DiscountController::class, 'update'])->name('discount.update');
    Route::delete('/discount/{discount}', [DiscountController::class, 'destroy'])->name('discount.destroy');


    Route::get('/discount-tambah', [DiscountController::class, 'create'])->name('discount.create');
    Route::post('/discount/store', [DiscountController::class, 'store'])->name('discount.store');
    Route::get('/produk-tambah', [ProdukController::class, 'create'])->name('produk.create');
    Route::post('/produk/store', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('/edit-produk={id}', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::post('/update-produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/hapus-produk={id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    Route::get('/stok', [StokController::class, 'index']);


    Route::get('/stok-tambah', [StokController::class, 'create'])->name('stok.create');
    Route::post('/stok-tambah', [StokController::class, 'store'])->name('stok.store');
    Route::get('/stok-edit={id}', [StokController::class, 'edit'])->name('stok.edit');
    Route::put('/stok-update/{id}', [StokController::class, 'update'])->name('stok.update');
    Route::delete('/stok-delete/{id}', [StokController::class, 'destroy'])->name('stok.destroy');

});
Route::get('/categories/search', [CategoryController::class, 'search'])->name('categories.search');
Route::get('/products/search', [ProdukController::class, 'search'])->name('products.search');

Route::get('/products/by-category', [ProdukController::class, 'getProductsByCategory'])->name('products.byCategory');

Route::middleware(['auth', 'onlyAdmin'])->group(function(){
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

    Route::get('/user', [UserController::class, 'index']);
    Route::get('/tambah-user', [UserController::class, 'tambah']);
    Route::post('/tambah-user', [UserController::class, 'insert']);
    Route::get('/edit-user={user:id}', [UserController::class, 'edit']);
    Route::put('/edit-user/{user:id}', [UserController::class, 'update']);
    Route::get('/hapus-user/{user:id}', [UserController::class, 'delete']);

    Route::get('/laporan', [LaporanController::class, 'index']);
    Route::post('/cari', [LaporanController::class, 'search']);
    Route::get('/detail={penjualan:kode_penjualan}', [LaporanController::class, 'show']);

    Route::get('/category', [CategoryController::class, 'index']);
    Route::get('/tambah-category', [CategoryController::class, 'tambah']);
    Route::post('/tambah-category', [CategoryController::class, 'insert']);
    Route::get('/edit-category={category:id}', [CategoryController::class, 'edit']);
    Route::put('/edit-category/{category:id}', [CategoryController::class, 'update']);
    Route::get('/hapus-category/{category:id}', [CategoryController::class, 'delete']);

});

Route::middleware(['auth', 'onlyPetugas'])->group(function(){
    Route::post('/tambah-produk', [ProdukController::class, 'insert']);
    Route::get('/edit-produk={produk:id}', [ProdukController::class, 'edit']);
    Route::put('/edit-produk/{produk:id}', [ProdukController::class, 'update']);
    Route::get('/hapus-produk/{produk:id}', [ProdukController::class, 'delete']);
});

Route::middleware(['auth', 'onlyKasir'])->group(function(){
    Route::get('/apply-discount/{detail_id}/{discount_id}', [PenjualanController::class, 'applyDiscount'])->name('apply.discount');
    Route::get('/cancel-discount/{detail_id}', [PenjualanController::class, 'cancelDiscount'])->name('cancel.discount');

    Route::get('/member', [MemberController::class, 'index']);
    Route::delete('/member/{id}', [MemberController::class, 'destroy'])->name('member.destroy');
    Route::get('/tambah-member', [MemberController::class, 'create'])->name('member.create');
    Route::post('/tambah-member', [MemberController::class, 'store'])->name('member.store');


    Route::get('/penjualan', [PenjualanController::class, 'index']);
    Route::post('/pilih-member', [PenjualanController::class, 'mulai'])->name('mulai.penjualan');
    Route::get('proses-penjualan={kode_penjualan}', [PenjualanController::class, 'proses'])->name('proses.penjualan');
    Route::post('/pilih-produk', [PenjualanController::class, 'store']);
    Route::get('/hapus-produk={detailPenjualan:id}', [PenjualanController::class, 'delete']);
    Route::post('/bayar/{penjualan:kode_penjualan}', [PenjualanController::class, 'bayar']);
    Route::get('/nota-penjualan/{penjualan:kode_penjualan}', [PenjualanController::class, 'nota']);
    Route::get('/hapus-penjualan/{penjualan:kode_penjualan}', [PenjualanController::class, 'deletePenjualan']);
});
