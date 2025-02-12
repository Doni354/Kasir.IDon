<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;

    protected $table = 'Stok'; // Pastikan sesuai dengan nama tabel
    protected $fillable = ['product_id', 'qty', 'expired_date', 'buy_date'];

    // Relasi ke model Product
    public function product()
    {
        return $this->belongsTo(Produk::class, 'product_id', 'id');
    }
}
