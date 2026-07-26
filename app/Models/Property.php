<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'owner_id',
        'property_name',
        'property_type',
        'total_rooms',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'pincode',
    ];

    // Relationship: Property belongs to Owner (User)
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
         * Relationship: Property has many Rooms
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function rentRecords()
    {
        return $this->hasMany(RentRecord::class);
    }
}
