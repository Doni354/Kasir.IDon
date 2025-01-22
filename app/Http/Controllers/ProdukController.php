<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(){
        $produk = Produk::all();
        return view('produk.produk', compact('produk'));
    }
    public function insert(Request $request){
        $request->validate([
            'nama' => 'required',
            'harga' => 'required',
            'stok' => 'required',
        ]);
        $produk = Produk::create($request->all());
        return back()->with('msg', 'Produk Berhasil ditambahkan');
    }

    public function edit(Produk $produk){
        return view('/produk.edit', compact('produk'));
    }

    public function update(Produk $produk, Request $request){
        $request->validate([
            'nama' => 'required',
            'harga' => 'required',
            'stok' => 'required',
        ]);
        $input = $request->all();
        $produk->fill($input)->save();
        return redirect('/produk')->with('msg', 'Produk Berhasil diedit');
    }

    public function delete(Produk $produk){
        $produk->delete();
        return back()->with('msg', 'Produk Berhasil dihapus');
    }
}
