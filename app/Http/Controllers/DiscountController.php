<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use App\Models\Produk;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $products = Produk::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();
        return view('discount.create', compact('products', 'categories'));
    }

    public function getProductsByCategory(Request $request)
    {
        $categoryId = $request->category_id;
        $today = date('Y-m-d');

        $products = Produk::where('status', 1)->where('category_id', $categoryId)
            ->whereNotIn('id', function($query) use ($today) {
                $query->select('product_id')
                      ->from('discount')
                      ->whereIn('status', [1, 0])
                      ->whereDate('valid_until', '>=', $today);
            })
            ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount' => 'required|numeric|min:1|max:100',
            'valid_until' => 'required|date',
            'category_id' => 'required|exists:category,id',
            'product_id' => 'required|exists:products,id',
            'min_qty' => 'required|numeric|min:1',
            'needed_poin' => 'nullable|numeric|min:0',
        ]);

        $discount = Discount::create([
            'name' => $request->name,
            'discount' => $request->discount,
            'valid_until' => $request->valid_until,
            'needed_poin' => $request->needed_poin,
            'min_qty' => $request->min_qty,
            'product_id' => $request->product_id,
            'category_id' => $request->category_id,
            'status' => 1,
        ]);

        // Log tindakan menambahkan diskon
        $this->logAction(Auth::id(), 'create', 'Discount', 'Diskon ' . $discount->name . ' telah ditambahkan.', $discount->id, null, $discount->toArray());

        return redirect('/discount')->with('msg', 'Diskon berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $discount = Discount::findOrFail($id);
        $categories = Category::where('status', 1)->get();
        $products = Produk::where('status', 1)->get();
        return view('discount.edit', compact('discount', 'categories', 'products'));
    }

    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'discount' => 'required|numeric|min:1|max:100',
            'valid_until' => 'required|date',
            'category_id' => 'required|exists:category,id',
            'product_id' => 'required|exists:products,id',
            'min_qty' => 'required|numeric|min:1',
            'needed_poin' => 'nullable|numeric|min:0',
        ]);

        $oldData = $discount->toArray();
        $discount->update($request->all());

        // Log tindakan memperbarui diskon
        $this->logAction(Auth::id(), 'update', 'Discount', 'Diskon ' . $discount->name . ' telah diperbarui.', $discount->id, $oldData, $discount->toArray());

        return redirect('/discount')->with('msg', 'Diskon berhasil diperbarui!');
    }

    public function destroy(Discount $discount)
    {
        $oldData = $discount->toArray();

        // Soft delete dengan mengubah status menjadi 0
        $discount->update(['status' => 0]);

        // Log tindakan menghapus diskon
        $this->logAction(Auth::id(), 'delete', 'Discount', 'Diskon ' . $discount->name . ' telah dihapus.', $discount->id, $oldData, null);

        return redirect('/discount')->with('msg', 'Diskon berhasil dihapus!');
    }

    private function logAction($userId, $action, $model, $msg, $recordId = null, $oldData = null, $newData = null)
    {
        \App\Models\Logs::create([
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
