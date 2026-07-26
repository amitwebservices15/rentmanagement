<?php
namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    // 📋 List all properties of logged-in owner
    public function index()
    {
        $properties = auth()->user()->properties;
        return view('properties.index', compact('properties'));
    }

    // ➕ Show create form
    public function create()
    {
        return view('properties.create');
    }

    // 💾 Store property
    public function store(Request $request)
    {
        $request->validate([
            'property_name'   => 'required|string|max:255',
            'property_type'   => 'nullable|in:hostel,pg,rooms,flats,commercial',
            'total_rooms'     => 'required|integer|min:1',
            'address_line_1'  => 'required|string|max:255',
            'address_line_2'  => 'nullable|string|max:255',
            'city'            => 'required|string|max:100',
            'state'           => 'required|string|max:100',
            'pincode'         => 'nullable|string|max:10',
        ]);

        Property::create([
            'owner_id'        => auth()->id(),
            'property_name'   => $request->property_name,
            'property_type'   => $request->property_type,
            'total_rooms'     => $request->total_rooms,
            'address_line_1'  => $request->address_line_1,
            'address_line_2'  => $request->address_line_2,
            'city'            => $request->city,
            'state'           => $request->state,
            'pincode'         => $request->pincode,
        ]);

        return redirect()->route('properties.index')
            ->with('success', 'Property created successfully');
    }

    // 👁 Show single property
    public function show(Property $property)
    {
        return view('properties.show', compact('property'));
    }

    // ✏️ Edit form
    public function edit(Property $property)
    {
        return view('properties.edit', compact('property'));
    }

    // 🔄 Update property
    public function update(Request $request, Property $property)
    {
        $request->validate([
            'property_name'   => 'required|string|max:255',
            'property_type'   => 'nullable|in:hostel,pg,rooms,flats,commercial',
            'total_rooms'     => 'required|integer|min:1',
            'address_line_1'  => 'required|string|max:255',
            'address_line_2'  => 'nullable|string|max:255',
            'city'            => 'required|string|max:100',
            'state'           => 'required|string|max:100',
            'pincode'         => 'nullable|string|max:10',
        ]);

        $property->update($request->all());

        return redirect()->route('properties.index')
            ->with('success', 'Property updated successfully');
    }

    // ❌ Delete property
    public function destroy(Property $property)
    {
        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', 'Property deleted successfully');
    }
}