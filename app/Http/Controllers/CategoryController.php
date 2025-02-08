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
        $categories = Category::all();
        return view('category.category', compact('categories'));

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

        $this->logAction(Auth::id(), 'create_category', 'Category', 'Category ' . $category->name . ' telah ditambahkan.');

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

        $category->name = $request->name;
        $category->save();

        $this->logAction(Auth::id(), 'update_category', 'Category', 'Category ' . $category->name . ' telah diperbarui.');

        return redirect('/category')->with('msg', 'Category berhasil diperbarui.');
    }

    public function delete(Category $category)
    {
        $this->logAction(Auth::id(), 'delete_category', 'Category', 'Category ' . $category->name . ' telah dihapus.');
        $category->delete();
        return back()->with('msg', 'Category berhasil dihapus.');
    }

    private function logAction($userId, $action, $model, $msg)
    {
        Logs::create([
            'user_id' => $userId,
            'action' => $action,
            'model' => $model,
            'msg' => $msg,
            'created_at' => now(),
        ]);
    }

    
}
