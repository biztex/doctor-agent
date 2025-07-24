<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'role',
        'content'
    ];

    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }
} 