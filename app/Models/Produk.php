<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = ['name', 'price', 'category_id'];

    // Relasi ke kategori
    public function kategori()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    // Relasi ke stok, menghitung total qty stok untuk produk ini
    public function totalStok()
    {
        return $this->hasMany(Stok::class, 'product_id', 'id') // Pastikan kolom yang benar
            ->where('expired_date', '>', now()) // Hanya stok yang belum expired
            ->sum('qty');
    }
}
