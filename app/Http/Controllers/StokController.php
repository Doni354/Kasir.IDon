<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stok;
use App\Models\Produk;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Auth;

class StokController extends Controller
{
    public function index()
    {
        $stokList = Stok::with('product')->get();
        return view('stok.stok', compact('stokList'));
    }

    public function create()
    {
        $products = Produk::all();
        return view('stok.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'qty'          => 'required|integer|min:1',
            'expired_date' => 'required|date',
            'buy_date'     => 'required|date',
        ]);

        $stok = Stok::create($request->all());

        LogHelper::logAction(
            'CREATE',
            'stok',
            $stok->id,
            "Stok untuk produk '{$stok->product->name}' telah ditambahkan.",
            [],
            $request->only(['product_id', 'qty', 'expired_date', 'buy_date'])
        );

        return redirect('/stok')->with('msg', 'Stok berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $stok = Stok::findOrFail($id);
        return view('stok.edit', compact('stok'));
    }

    public function update(Request $request, $id)
    {
        $stok = Stok::findOrFail($id);
        $oldData = $stok->only(['product_id', 'qty', 'expired_date', 'buy_date']);

        $stok->update($request->all());

        $newData = $stok->only(['product_id', 'qty', 'expired_date', 'buy_date']);
        $changes = array_diff_assoc($newData, $oldData);

        if (!empty($changes)) {
            LogHelper::logAction(
                'UPDATE',
                'stok',
                $stok->id,
                "Stok untuk produk '{$stok->product->name}' telah diperbarui.",
                $oldData,
                $changes
            );
        }

        return redirect('/stok')->with('success', 'Data stok berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $stok = Stok::findOrFail($id);
        $oldData = $stok->only(['product_id', 'qty', 'expired_date', 'buy_date']);

        $stok->delete();

        LogHelper::logAction(
            'DELETE',
            'stok',
            $stok->id,
            "Stok untuk produk '{$stok->product->name}' telah dihapus.",
            $oldData,
            []
        );

        return redirect('/stok')->with('success', 'Data stok berhasil dihapus.');
    }
}
