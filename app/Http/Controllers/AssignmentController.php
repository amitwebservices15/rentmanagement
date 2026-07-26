<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomTenantAssignment;
use App\Models\Tenant;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function create(Tenant $tenant)
    {
        abort_if($tenant->owner_id !== auth()->id(), 403);

        // Load all rooms with active assignment count so view can show occupancy
        $properties = auth()->user()->properties()
            ->with(['rooms' => function ($q) {
                $q->withCount(['assignments as active_count' => fn($q) => $q->where('status', 'active')])
                  ->orderBy('room_number');
            }])
            ->get();

        return view('assignments.create', compact('tenant', 'properties'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        abort_if($tenant->owner_id !== auth()->id(), 403);

        $request->validate([
            'room_id'                 => 'required|exists:rooms,id',
            'rent_amount'             => 'required|numeric|min:0',
            'electricity_meter_start' => 'nullable|numeric|min:0',
            'start_date'              => 'required|date',
        ]);

        // Verify room belongs to this owner
        $room = Room::whereHas('property', fn($q) => $q->where('owner_id', auth()->id()))
                    ->findOrFail($request->room_id);

        // Check capacity
        if ($room->isFull()) {
            return back()->withErrors(['room_id' => "Room {$room->room_number} is full (capacity: {$room->capacity})."]);
        }

        // Prevent tenant from having two active assignments
        if ($tenant->activeAssignment) {
            return back()->withErrors(['room_id' => 'This tenant already has an active room assignment.']);
        }

        RoomTenantAssignment::create([
            'room_id'                 => $room->id,
            'tenant_id'               => $tenant->id,
            'rent_amount'             => $request->rent_amount,
            'electricity_meter_start' => $request->electricity_meter_start,
            'start_date'              => $request->start_date,
            'status'                  => 'active',
        ]);

        // Mark tenant as active since they now have an active assignment
        $tenant->update(['status' => 'active']);

        // Mark room occupied only when full
        $room->update(['status' => $room->isFull() ? 'occupied' : 'available']);

        return redirect()->route('tenants.index')
            ->with('success', "Room {$room->room_number} assigned to {$tenant->name}.");
    }

    public function vacate(RoomTenantAssignment $assignment)
    {
        abort_if($assignment->room->property->owner_id !== auth()->id(), 403);

        $assignment->update([
            'status'   => 'vacated',
            'end_date' => now()->toDateString(),
        ]);

        // Update tenant status to inactive since they no longer have an active assignment
        $tenant = $assignment->tenant;
        $tenant->update(['status' => 'inactive']);

        // Refresh room — mark available since a slot just opened
        $assignment->room->update(['status' => 'available']);

        return back()->with('success', "Tenant {$tenant->name} vacated successfully and marked as inactive.");
    }
}
