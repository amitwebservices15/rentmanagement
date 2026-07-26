<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditPack;
use Illuminate\Http\Request;

class CreditPackController extends Controller
{
    public function index()
    {
        $packs = CreditPack::orderBy('price')->get();
        return view('admin.credits.index', compact('packs'));
    }

    public function create()
    {
        return view('admin.credits.form', ['pack' => new CreditPack]);
    }

    public function store(Request $request)
    {
        CreditPack::create($this->validated($request));
        return redirect()->route('admin.credits.index')
            ->with('success', 'Credit pack created.');
    }

    public function edit(CreditPack $credit)
    {
        return view('admin.credits.form', ['pack' => $credit]);
    }

    public function update(Request $request, CreditPack $credit)
    {
        $credit->update($this->validated($request));
        return redirect()->route('admin.credits.index')
            ->with('success', 'Credit pack updated.');
    }

    public function destroy(CreditPack $credit)
    {
        $credit->delete();
        return back()->with('success', 'Credit pack deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'      => 'required|string|max:100',
            'price'     => 'required|numeric|min:0',
            'credits'   => 'required|integer|min:1',
            'tag'       => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
    }
}
