<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'mobile',
        'phone', // Add phone field
        'email',
        'photo',
        'id_proof_number',
        'id_proof',
        'id_proof_back',
        'address',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function activeAssignment()
    {
        return $this->hasOne(RoomTenantAssignment::class)->where('status', 'active')->latest();
    }

    public function assignments()
    {
        return $this->hasMany(RoomTenantAssignment::class);
    }

    public function rentRecords()
    {
        return $this->hasMany(RentRecord::class);
    }

    public function whatsappMessages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    // Get phone number (use mobile if phone is not set)
    public function getPhoneAttribute()
    {
        return $this->attributes['phone'] ?? $this->mobile;
    }
}