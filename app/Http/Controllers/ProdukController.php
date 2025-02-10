<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Produk;
use App\Models\logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::with('kategori')->get();
        return view('produk.produk', compact('produk'));
    }

// Menampilkan form tambah produk
    public function create()
    {
        $categories = Category::all(); // Ambil semua kategori
        return view('produk.create', compact('categories'));
    }

    // Menyimpan data produk baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:category,id',
        ]);

        // Simpan data produk baru
        $produk = Produk::create([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
        ]);

        // Simpan log aksi
        Logs::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'Produk',
            'msg' => 'Produk ' . $produk->name . ' telah ditambahkan.',
            'created_at' => now(),
        ]);
        return redirect('/produk')->with('msg', 'Produk berhasil ditambahkan.');

    }

    public function insert(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id'
        ]);

        Produk::create($request->all());
        return back()->with('msg', 'Produk Berhasil ditambahkan');
    }

    public function edit(Produk $produk)
    {
        $kategori = Category::all(); // Ambil kategori untuk dropdown
        return view('produk.edit', compact('produk', 'kategori'));
    }

    public function update(Produk $produk, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id'
        ]);

        $produk->update($request->all());
        return redirect('/produk')->with('msg', 'Produk Berhasil diedit');
    }

    public function delete(Produk $produk)
    {
        $produk->delete();
        return back()->with('msg', 'Produk Berhasil dihapus');
    }
}
