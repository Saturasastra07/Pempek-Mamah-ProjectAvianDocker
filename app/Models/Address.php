<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id', 
        'label', 
        'receiver_name', 
        'phone_number', 
        'full_address', 
        'district', 
        'city', 
        'is_default',
        'lat',
        'lng'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}