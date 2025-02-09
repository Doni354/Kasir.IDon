<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Produk;

use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::with('kategori')->get();
        return view('produk.produk', compact('produk'));
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
