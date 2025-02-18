<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
{
    // Ambil hanya kategori dengan status 1 (aktif)
    $categories = Category::where('status', 1)->get();
    return view('category.category', compact('categories'));
}

    public function search(Request $request)
    {
        $search = $request->input('q');
        $categories = Category::where('status', 1)->where('name', 'LIKE', "%{$search}%")->get(['id', 'name']);
        return response()->json($categories);
    }

    public function tambah()
    {
        return view('category.tambah');
    }

    public function insert(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:category,name|max:255',
        ]);

        $category = new Category;
        $category->name = $request->name;
        $category->save();

        $this->logAction(Auth::id(), 'create', 'Category', 'Category ' . $category->name . ' telah ditambahkan.', $category->id, null, $category->toArray());

        return redirect('/category')->with('msg', 'Category berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('category.edit', compact('category'));
    }

    public function update(Category $category, Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:category,name,' . $category->id,
        ]);

        $oldData = $category->toArray();

        $category->name = $request->name;
        $category->save();

        $this->logAction(Auth::id(), 'update', 'Category', 'Category ' . $category->name . ' telah diperbarui.', $category->id, $oldData, $category->toArray());

        return redirect('/category')->with('msg', 'Category berhasil diperbarui.');
    }

    public function delete(Category $category)
    {
        $oldData = $category->toArray();

        // Update status menjadi 0
        $category->update(['status' => 0]);

        $this->logAction(Auth::id(), 'delete', 'Category', 'Category ' . $category->name . ' telah dinonaktifkan.', $category->id, $oldData, null);

        return back()->with('msg', 'Category berhasil dinonaktifkan.');
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
