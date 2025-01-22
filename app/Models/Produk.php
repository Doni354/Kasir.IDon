<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function detailPenjualan(){
        return $this->hasMany(detailPenjualan::class);
    }

    public static function boot(){
        parent::boot();
        self::creating(function($produk){
            $latestKdProduk = self::latest('kode_produk')->firstOrNew([]);
            $currentNumber = (int) substr($latestKdProduk->kode_produk, 2);
            $produk->kode_produk = 'PR' . str_pad(++$currentNumber, 4 , '0', STR_PAD_LEFT);
        });
    }
}
