<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'room_id',
        'property_id',
        'month',
        'rent_amount',
        'electricity_units',
        'electricity_charge',
        'other_charges',
        'previous_due',
        'advance_amount',
        'meter_start',
        'meter_end',
        'tenant_names',
        'total_amount',
        'paid_amount',
        'due_amount',
        'status',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // tenant_id kept for backward compat but record is room-based
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
