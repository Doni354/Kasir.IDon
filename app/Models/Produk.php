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
        return $this->belongsTo(Category::class, 'category_id');
    }
}
