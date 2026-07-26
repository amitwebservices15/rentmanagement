<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'room_number',
        'floor',
        'capacity',
        'rent_amount',
        'status',
        'description',
    ];

    /**
     * Relationship: Room belongs to Property
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function activeAssignment()
    {
        return $this->hasOne(RoomTenantAssignment::class)->where('status', 'active')->latest();
    }

    public function activeAssignments()
    {
        return $this->hasMany(RoomTenantAssignment::class)->where('status', 'active');
    }

    public function isFull(): bool
    {
        return $this->activeAssignments()->count() >= $this->capacity;
    }

    public function availableSlots(): int
    {
        return max(0, $this->capacity - $this->activeAssignments()->count());
    }

    public function assignments()
    {
        return $this->hasMany(RoomTenantAssignment::class);
    }

    public function rentRecords()
    {
        return $this->hasMany(RentRecord::class);
    }
}
