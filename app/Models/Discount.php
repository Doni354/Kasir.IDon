<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Discount extends Model
{
    use HasFactory;

    protected $table = 'discount';
    protected $fillable = ['name', 'discount', 'needed_poin', 'min_qty', 'min_price', 'product_id', 'category_id', 'valid_until', 'status'];

    public function product()
    {
        return $this->belongsTo(Produk::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}


