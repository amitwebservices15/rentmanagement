<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTenantAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'tenant_id',
        'rent_amount',
        'electricity_meter_start',
        'electricity_meter_end',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Assignment belongs to Room
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Assignment belongs to Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}