<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stok;
use App\Models\Produk;
use Carbon\Carbon;

class StokController extends Controller
{
    public function index()
    {
        // Ambil data stok dengan relasi ke produk
        $stokList = Stok::with('product')->get();
        return view('stok.stok', compact('stokList'));
    }

    public function create()
    {
        // Ambil semua produk untuk select option
        $products = Produk::all();
        return view('stok.create', compact('products'));
    }

    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'expired_date' => 'required|date|after:today',
            'buy_date' => 'required|date|after_or_equal:today',
        ], [
            'product_id.required' => 'Produk harus dipilih.',
            'qty.required' => 'Jumlah stok wajib diisi.',
            'qty.min' => 'Jumlah stok harus lebih dari 0.',
            'expired_date.required' => 'Tanggal kedaluwarsa harus diisi.',
            'expired_date.after' => 'Tanggal kedaluwarsa harus lebih dari hari ini.',
            'buy_date.required' => 'Tanggal beli harus diisi.',
            'buy_date.after_or_equal' => 'Tanggal beli tidak boleh sebelum hari ini.',
        ]);

        // Simpan ke database
        Stok::create([
            'product_id' => $request->product_id,
            'qty' => $request->qty,
            'expired_date' => $request->expired_date,
            'buy_date' => $request->buy_date,
        ]);
        return redirect('/stok')->with('msg', 'Stok berhasil ditambahkan!');

    }
}
