<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
        $currentSubscription = auth()->user()->subscriptions()->where('status', 'active')->first();
        
        return view('owner.subscriptions.index', compact('plans', 'currentSubscription'));
    }

    public function purchase(SubscriptionPlan $plan)
    {
        return view('owner.subscriptions.purchase', compact('plan'));
    }

    public function processPurchase(Request $request, SubscriptionPlan $plan)
    {
        $user = auth()->user();
        
        // Cancel existing active subscription
        $user->subscriptions()->where('status', 'active')->update(['status' => 'cancelled']);
        
        // Create new subscription
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'amount_paid' => $plan->price,
            'start_date' => now(),
            'end_date' => now()->addDays($plan->validity_days),
            'message_credits_remaining' => $plan->message_credits,
            'status' => 'active',
            'plan_snapshot' => $plan->toArray(),
        ]);

        // Add credits to user
        $user->increment('message_credits', $plan->message_credits);

        return redirect()->route('owner.subscriptions.index')
            ->with('success', 'Subscription purchased successfully!');
    }

    public function mySubscriptions()
    {
        $subscriptions = auth()->user()->subscriptions()->with('plan')->latest()->get();
        return view('owner.subscriptions.my-subscriptions', compact('subscriptions'));
    }
}