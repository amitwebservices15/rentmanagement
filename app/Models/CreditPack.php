<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPack extends Model
{
    protected $fillable = [
        'name', 'price', 'credits', 'tag', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
