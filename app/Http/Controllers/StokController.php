<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stok;
use App\Models\Product;

class StokController extends Controller
{
    public function index()
    {
        // Ambil data stok dengan relasi ke produk
        $stokList = Stok::with('product')->get();
        return view('stok.stok', compact('stokList'));
    }
}
