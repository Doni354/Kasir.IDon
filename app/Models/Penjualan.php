<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function detailPenjualan(){
        return $this->hasMany(detailPenjualan::class, 'kode_penjualan');
    }
    public function pelanggan(){
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function boot(){
        parent::boot();
        self::creating(function($penjualan){
            $latestKdPenjualan = self::latest('kode_penjualan')->firstOrNew([]);
            $currentNumber = (int) substr($latestKdPenjualan->kode_penjualan, 2);
            $penjualan->kode_penjualan = 'TR' . str_pad(++$currentNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}
