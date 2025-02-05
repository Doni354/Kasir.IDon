<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class logs extends Model
{
    use HasFactory;

    protected $table = 'logs';
    protected $fillable = ['user_id', 'action', 'model', 'msg', 'created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
