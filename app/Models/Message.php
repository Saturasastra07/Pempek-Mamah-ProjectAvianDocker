<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'is_read'  => 'boolean',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isFromBot()
    {
        return $this->sender_type === 'bot';
    }

    public function isFromAdmin()
    {
        return $this->sender_type === 'admin';
    }
}