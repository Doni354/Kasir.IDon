<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use App\Models\Produk;
use App\Models\Category;
use Carbon\Carbon;

class DiscountController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $discounts = Discount::where('status', 1)->get();

        return view('discount.discount', compact('discounts', 'today'));
    }

    public function create()
{
    $products = Produk::all(); // Ambil produk dari tabel 'products'
    $categories = Category::all(); // Ambil kategori dari tabel 'category'

    return view('discount.create', compact('products', 'categories'));
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'discount' => 'required|numeric|min:1|max:100',
        'valid_until' => 'required|date',
        'needed_poin' => 'nullable|integer|min:0',
        'min_qty' => 'nullable|integer|min:1',
        'min_price' => 'nullable|numeric|min:0',
        'product_id' => 'nullable|exists:products,id',
        'category_id' => 'nullable|exists:category,id',
    ]);

    Discount::create([
        'name' => $request->name,
        'discount' => $request->discount,
        'valid_until' => $request->valid_until,
        'needed_poin' => $request->needed_poin,
        'min_qty' => $request->min_qty,
        'min_price' => $request->min_price,
        'product_id' => $request->product_id,
        'category_id' => $request->category_id,
        'status' => 1,
    ]);

    return redirect('/discount')->with('msg', 'Diskon berhasil ditambahkan!');
}
public function edit($id)
{
    $discount = Discount::findOrFail($id);
    $categories = Category::all();
    $products = Produk::all();

    return view('discount.edit', compact('discount', 'categories', 'products'));
}
public function update(Request $request, $id)
{
    $discount = Discount::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'discount' => 'required|numeric|min:1|max:100',
        'valid_until' => 'required|date',
        'category_id' => 'nullable|exists:category,id',
        'product_id' => 'nullable|exists:products,id',
        'min_qty' => 'nullable|numeric|min:0',
        'min_price' => 'nullable|numeric|min:0',
        'needed_poin' => 'nullable|numeric|min:0',
    ]);

    $discount->update($request->all());

    return redirect('/discount')->with('msg', 'Diskon berhasil diperbarui!');
}
public function destroy(Discount $discount)
{
    $discount->update(['status' => 0]); // Soft delete dengan mengubah status
    return redirect('/discount')->with('msg', 'Diskon berhasil dihapus!');
}


}
