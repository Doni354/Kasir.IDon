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

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        $categories = Category::all(); // Ambil semua kategori

        return view('produk.edit', compact('produk', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:category,id',
        ]);

        $produk = Produk::findOrFail($id);
        $produk->update([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
        ]);

        // Log perubahan
        Logs::create([
            'user_id' => Auth::id(),
            'action' => 'update_product',
            'model' => 'Produk',
            'msg' => 'Produk ' . $produk->name . ' telah diperbarui.',
        ]);

        return redirect('/produk')->with('msg', 'Produk berhasil diperbarui.');
    }
    public function destroy($id)
{
    $produk = Produk::findOrFail($id);

    // Simpan log sebelum menghapus
    Logs::create([
        'user_id' => Auth::id(),
        'action' => 'delete_product',
        'model' => 'Produk',
        'msg' => 'Produk ' . $produk->name . ' telah dihapus.',
    ]);

    // Hapus produk
    $produk->delete();

    return redirect('/produk')->with('msg', value: 'Produk berhasil dihapus.');
}



}
