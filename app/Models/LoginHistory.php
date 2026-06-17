<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'device_name', 'location', 'ip_address', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}