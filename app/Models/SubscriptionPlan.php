<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'price', 'validity_days', 'message_credits',
        'max_properties', 'max_rooms', 'features',
        'is_active', 'is_popular',
    ];

    protected $casts = [
        'features'   => 'array',
        'is_active'  => 'boolean',
        'is_popular' => 'boolean',
    ];
}
