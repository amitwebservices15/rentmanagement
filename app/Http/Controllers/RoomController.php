<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Room;
use App\Models\RoomTenantAssignment;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    private function getProperty(int $propertyId): Property
    {
        return auth()->user()->properties()->findOrFail($propertyId);
    }

    public function index(int $propertyId)
    {
        $property = $this->getProperty($propertyId);
        $rooms = $property->rooms()
            ->withCount(['assignments as active_count' => fn($q) => $q->where('status', 'active')])
            ->orderBy('room_number')
            ->get();
        return view('rooms.index', compact('property', 'rooms'));
    }

    public function create(int $propertyId)
    {
        $property = $this->getProperty($propertyId);
        return view('rooms.create', compact('property'));
    }

    public function store(Request $request, int $propertyId)
    {
        $property = $this->getProperty($propertyId);

        $request->validate([
            'room_number' => ['required', 'string', 'max:20', Rule::unique('rooms')->where('property_id', $property->id)],
            'floor'       => 'nullable|string|max:20',
            'capacity'    => 'required|integer|min:1',
            'rent_amount' => 'required|numeric|min:0',
            'status'      => 'required|in:available,occupied',
            'description' => 'nullable|string|max:500',
        ]);

        $property->rooms()->create($request->only('room_number', 'floor', 'capacity', 'rent_amount', 'status', 'description'));

        return redirect()->route('properties.rooms.index', $property)
            ->with('success', 'Room added successfully.');
    }

    public function edit(int $propertyId, Room $room)
    {
        $property = $this->getProperty($propertyId);
        abort_if($room->property_id !== $property->id, 403);

        // Current active tenants in this room
        $activeAssignments = $room->assignments()
            ->with('tenant')
            ->where('status', 'active')
            ->get();

        // Tenants belonging to this owner who are NOT currently assigned anywhere
        $unassignedTenants = auth()->user()->tenants()
            ->whereDoesntHave('assignments', fn($q) => $q->where('status', 'active'))
            ->orderBy('name')
            ->get();

        return view('rooms.edit', compact('property', 'room', 'activeAssignments', 'unassignedTenants'));
    }

    public function update(Request $request, int $propertyId, Room $room)
    {
        $property = $this->getProperty($propertyId);
        abort_if($room->property_id !== $property->id, 403);

        $request->validate([
            'room_number' => ['required', 'string', 'max:20', Rule::unique('rooms')->where('property_id', $property->id)->ignore($room->id)],
            'floor'       => 'nullable|string|max:20',
            'capacity'    => 'required|integer|min:1',
            'rent_amount' => 'required|numeric|min:0',
            'status'      => 'required|in:available,occupied',
            'description' => 'nullable|string|max:500',
        ]);

        $room->update($request->only('room_number', 'floor', 'capacity', 'rent_amount', 'status', 'description'));

        return redirect()->route('properties.rooms.edit', [$property, $room])
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(int $propertyId, Room $room)
    {
        $property = $this->getProperty($propertyId);
        abort_if($room->property_id !== $property->id, 403);
        $room->delete();

        return redirect()->route('properties.rooms.index', $property)
            ->with('success', 'Room deleted successfully.');
    }

    // ── Tenant Management from Room Edit ──────────────────────────────

    /**
     * Add a new tenant and assign to this room, OR assign an existing tenant.
     */
    public function tenantStore(Request $request, int $propertyId, Room $room)
    {
        $property = $this->getProperty($propertyId);
        abort_if($room->property_id !== $property->id, 403);

        if ($room->isFull()) {
            return back()->withErrors(['tenant' => "Room {$room->room_number} is full (capacity: {$room->capacity})."]);
        }

        $request->validate([
            'tenant_mode'             => 'required|in:new,existing',
            // new tenant fields
            'name'                    => 'required_if:tenant_mode,new|string|max:255',
            'mobile'                  => 'required_if:tenant_mode,new|string|max:20|unique:tenants,mobile',
            'email'                   => 'nullable|email|max:255',
            'id_proof_number'         => 'nullable|string|max:100',
            'address'                 => 'nullable|string|max:500',
            'photo'                   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'id_proof'                => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:3072',
            'id_proof_back'           => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:3072',
            // existing tenant
            'tenant_id'               => 'nullable|exists:tenants,id',
            // assignment fields
            'rent_amount'             => 'required|numeric|min:0',
            'electricity_meter_start' => 'nullable|numeric|min:0',
            'start_date'              => 'required|date',
        ]);

        if ($request->tenant_mode === 'new') {
            $data = $request->only('name', 'mobile', 'email', 'id_proof_number', 'address');
            $data['status'] = 'active';

            foreach (['photo' => 'tenants/photos', 'id_proof' => 'tenants/id_proofs', 'id_proof_back' => 'tenants/id_proofs'] as $field => $path) {
                if ($request->hasFile($field)) {
                    $data[$field] = $request->file($field)->store($path, 'public');
                }
            }

            $tenant = auth()->user()->tenants()->create($data);
        } else {
            if (!$request->tenant_id) {
                return back()->withErrors(['tenant_id' => 'Please select a tenant.']);
            }
            $tenant = auth()->user()->tenants()->findOrFail($request->tenant_id);

            if ($tenant->activeAssignment) {
                return back()->withErrors(['tenant_id' => 'This tenant already has an active room assignment.']);
            }
        }

        RoomTenantAssignment::create([
            'room_id'                 => $room->id,
            'tenant_id'               => $tenant->id,
            'rent_amount'             => $request->rent_amount,
            'electricity_meter_start' => $request->electricity_meter_start,
            'start_date'              => $request->start_date,
            'status'                  => 'active',
        ]);

        $room->update(['status' => $room->isFull() ? 'occupied' : 'available']);

        return redirect()->route('properties.rooms.edit', [$property, $room])
            ->with('success', "Tenant {$tenant->name} added to Room {$room->room_number}.");
    }

    /**
     * Update an existing tenant's details from the room edit page.
     */
    public function tenantUpdate(Request $request, int $propertyId, Room $room, Tenant $tenant)
    {
        $property = $this->getProperty($propertyId);
        abort_if($room->property_id !== $property->id, 403);
        abort_if($tenant->owner_id !== auth()->id(), 403);

        $request->validate([
            'name'            => 'required|string|max:255',
            'mobile'          => 'required|string|max:20|unique:tenants,mobile,' . $tenant->id,
            'email'           => 'nullable|email|max:255',
            'id_proof_number' => 'nullable|string|max:100',
            'address'         => 'nullable|string|max:500',
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'id_proof'        => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:3072',
            'id_proof_back'   => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:3072',
        ]);

        $data = $request->only('name', 'mobile', 'email', 'id_proof_number', 'address');

        foreach (['photo' => 'tenants/photos', 'id_proof' => 'tenants/id_proofs', 'id_proof_back' => 'tenants/id_proofs'] as $field => $path) {
            if ($request->hasFile($field)) {
                if ($tenant->$field) Storage::disk('public')->delete($tenant->$field);
                $data[$field] = $request->file($field)->store($path, 'public');
            }
        }

        $tenant->update($data);

        return redirect()->route('properties.rooms.edit', [$property, $room])
            ->with('success', "Tenant {$tenant->name} updated.");
    }

    /**
     * Vacate a tenant from this room.
     */
    public function tenantVacate(int $propertyId, Room $room, RoomTenantAssignment $assignment)
    {
        $property = $this->getProperty($propertyId);
        abort_if($room->property_id !== $property->id, 403);
        abort_if($assignment->room_id !== $room->id, 403);

        $assignment->update([
            'status'   => 'vacated',
            'end_date' => now()->toDateString(),
        ]);

        $room->update(['status' => 'available']);

        return redirect()->route('properties.rooms.edit', [$property, $room])
            ->with('success', 'Tenant vacated successfully.');
    }
}
