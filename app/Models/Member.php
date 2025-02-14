<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'member';

    protected $fillable = [
        'nama',
        'email',
        'phone',
        'poin',
        'type',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Set default value untuk poin dan type
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($member) {
            $member->poin = 0; // Poin default 0
            $member->type = 1; // Type default (1 = Bronze)
        });
    }
}
