<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'amount_paid',
        'start_date',
        'end_date',
        'message_credits_remaining',
        'status',
        'plan_snapshot',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'plan_snapshot' => 'array',
        'amount_paid' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->end_date >= now()->toDateString();
    }

    public function isExpired()
    {
        return $this->end_date < now()->toDateString();
    }
}