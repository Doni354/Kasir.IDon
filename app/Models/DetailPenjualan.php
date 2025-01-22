<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    public function penjualan(){
        return $this->belongsTo(Penjualan::class, 'kode_penjualan');
    }
    public function produk(){
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    protected $guarded = ['id'];
}
