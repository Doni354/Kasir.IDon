<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class logs extends Model
{
    use HasFactory;

    protected $table = 'logs';
    protected $fillable = [
        'user_id', 'action', 'table_name', 'record_id',
        'old_data', 'new_data', 'msg', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
