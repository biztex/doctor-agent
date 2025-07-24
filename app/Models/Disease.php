<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'urgency_level'
    ];

    protected $casts = [
        'urgency_level' => 'string'
    ];

    public function getUrgencyLevelClassAttribute()
    {
        switch ($this->urgency_level) {
            case 'レベル1':
                return 'bg-green-100 text-green-800';
            case 'レベル2':
                return 'bg-yellow-100 text-yellow-800';
            case 'レベル3':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
} 