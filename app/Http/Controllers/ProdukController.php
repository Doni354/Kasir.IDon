<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Produk;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::with('kategori')->where('status', 1)->get();

        return view('produk.produk', compact('produk'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('produk.create', compact('categories'));
    }
    public function getProductsByCategory(Request $request)
    {
        $products = Produk::where('category_id', $request->category_id)->get();
        return response()->json($products);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:category,id',
        ]);

        $produk = Produk::create([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
        ]);

        $this->logAction(Auth::id(), 'create', 'Produk', 'Produk ' . $produk->name . ' telah ditambahkan.', $produk->id, null, $produk->toArray());

        return redirect('/produk')->with('msg', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        $categories = Category::all();
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
        $oldData = $produk->toArray();

        $produk->update([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
        ]);

        $this->logAction(Auth::id(), 'update', 'Produk', 'Produk ' . $produk->name . ' telah diperbarui.', $produk->id, $oldData, $produk->toArray());

        return redirect('/produk')->with('msg', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
{
    $produk = Produk::findOrFail($id);
    $oldData = $produk->toArray();

    // Soft delete dengan update status menjadi 0
    $produk->update(['status' => 0]);

    // Log perubahan
    $this->logAction(Auth::id(), 'delete', 'Produk', 'Produk ' . $produk->name . ' telah dinonaktifkan.', $produk->id, $oldData, null);

    return redirect('/produk')->with('msg', 'Produk berhasil dinonaktifkan.');
}


    public function search(Request $request)
    {
        $search = $request->input('q');
        $products = Produk::where('name', 'LIKE', "%{$search}%")->get(['id', 'name']);
        return response()->json($products);
    }

    private function logAction($userId, $action, $model, $msg, $recordId = null, $oldData = null, $newData = null)
    {
        Logs::create([
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $model,
            'record_id' => $recordId,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'msg' => $msg,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'created_at' => now(),
        ]);
    }
}
