<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('price')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.form', ['plan' => new SubscriptionPlan]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['features'] = $this->parseFeatures($request->features_raw);
        SubscriptionPlan::create($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Subscription plan created.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.plans.form', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $data = $this->validated($request);
        $data['features'] = $this->parseFeatures($request->features_raw);
        $plan->update($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan updated.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $plan->delete();
        return back()->with('success', 'Plan deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'            => 'required|string|max:100',
            'price'           => 'required|numeric|min:0',
            'validity_days'   => 'required|integer|min:1',
            'message_credits' => 'required|integer|min:0',
            'max_properties'  => 'required|integer|min:1',
            'max_rooms'       => 'required|integer|min:1',
            'is_active'       => 'boolean',
            'is_popular'      => 'boolean',
        ]);
    }

    private function parseFeatures(?string $raw): array
    {
        if (!$raw) return [];
        return array_values(array_filter(array_map('trim', explode("\n", $raw))));
    }
}
