<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    protected $fillable = [
        'user_id',
        'tenant_id',
        'rent_record_id',
        'phone_number',
        'message',
        'status',
        'whatsapp_message_id',
        'credits_used',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function rentRecord()
    {
        return $this->belongsTo(RentRecord::class);
    }
}