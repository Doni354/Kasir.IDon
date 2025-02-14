<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'stok_terpakai' => 'array', // Simpan data stok dalam format array
    ];
    public function penjualan(){
        return $this->belongsTo(Penjualan::class, 'kode_penjualan');
    }
    public function produk(){
        return $this->belongsTo(Produk::class, 'produk_id');
    }

}
