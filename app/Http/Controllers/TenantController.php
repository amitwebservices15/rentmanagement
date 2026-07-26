<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = auth()->user()->tenants()
            ->with('activeAssignment.room.property')
            ->orderBy('name')
            ->get();

        // Update tenant status based on room assignment
        foreach ($tenants as $tenant) {
            $hasActiveAssignment = $tenant->activeAssignment !== null;
            
            // If tenant has no active assignment but is marked as active, make them inactive
            if (!$hasActiveAssignment && $tenant->status === 'active') {
                $tenant->update(['status' => 'inactive']);
            }
            // If tenant has active assignment but is marked as inactive, make them active
            elseif ($hasActiveAssignment && $tenant->status === 'inactive') {
                $tenant->update(['status' => 'active']);
            }
        }

        // Refresh the collection to get updated status
        $tenants = auth()->user()->tenants()
            ->with('activeAssignment.room.property')
            ->orderBy('name')
            ->get();

        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'mobile'          => 'required|string|max:20|unique:tenants,mobile',
            'email'           => 'nullable|email|max:255',
            'id_proof_number' => 'nullable|string|max:100',
            'address'         => 'nullable|string|max:500',
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'id_proof'        => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:3072',
            'id_proof_back'   => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:3072',
        ]);

        $data = $request->only('name', 'mobile', 'email', 'id_proof_number', 'address');
        
        // New tenants start as inactive until assigned to a room
        $data['status'] = 'inactive';

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('tenants/photos', 'public');
        }
        if ($request->hasFile('id_proof')) {
            $data['id_proof'] = $request->file('id_proof')->store('tenants/id_proofs', 'public');
        }
        if ($request->hasFile('id_proof_back')) {
            $data['id_proof_back'] = $request->file('id_proof_back')->store('tenants/id_proofs', 'public');
        }

        auth()->user()->tenants()->create($data);

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant added successfully.');
    }

    public function edit(Tenant $tenant)
    {
        abort_if($tenant->owner_id !== auth()->id(), 403);
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        abort_if($tenant->owner_id !== auth()->id(), 403);

        $request->validate([
            'name'            => 'required|string|max:255',
            'mobile'          => 'required|string|max:20|unique:tenants,mobile,' . $tenant->id,
            'email'           => 'nullable|email|max:255',
            'id_proof_number' => 'nullable|string|max:100',
            'address'         => 'nullable|string|max:500',
            'status'          => 'required|in:active,inactive',
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'id_proof'        => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:3072',
            'id_proof_back'   => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:3072',
        ]);

        $data = $request->only('name', 'mobile', 'email', 'id_proof_number', 'address', 'status');

        if ($request->hasFile('photo')) {
            if ($tenant->photo) Storage::disk('public')->delete($tenant->photo);
            $data['photo'] = $request->file('photo')->store('tenants/photos', 'public');
        }
        if ($request->hasFile('id_proof')) {
            if ($tenant->id_proof) Storage::disk('public')->delete($tenant->id_proof);
            $data['id_proof'] = $request->file('id_proof')->store('tenants/id_proofs', 'public');
        }
        if ($request->hasFile('id_proof_back')) {
            if ($tenant->id_proof_back) Storage::disk('public')->delete($tenant->id_proof_back);
            $data['id_proof_back'] = $request->file('id_proof_back')->store('tenants/id_proofs', 'public');
        }

        $tenant->update($data);

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        abort_if($tenant->owner_id !== auth()->id(), 403);

        // Delete uploaded files
        Storage::disk('public')->delete(array_filter([
            $tenant->photo,
            $tenant->id_proof,
            $tenant->id_proof_back,
        ], fn($v) => !is_null($v)));

        $tenant->delete();

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }
}
