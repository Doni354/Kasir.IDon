<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Discount extends Model
{
    use HasFactory;

    protected $table = 'discount';


    protected $fillable = [
        'name', 'discount', 'needed_poin', 'min_qty', 'min_price',
        'product_id', 'valid_until', 'status'
    ];

    public function scopeValidDiscount($query, $product_id, $qty)
    {
        return $query->where('product_id', $product_id)
            ->where('status', 1)
            ->where('valid_until', '>=', now())
            ->where('min_qty', '<=', $qty);
    }

    public function product()
    {
        return $this->belongsTo(Produk::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}


