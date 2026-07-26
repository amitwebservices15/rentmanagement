<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CreditPack;
use App\Models\UserCreditPurchase;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index()
    {
        $packs = CreditPack::where('is_active', true)->orderBy('price')->get();
        $user = auth()->user();
        
        return view('owner.credits.index', compact('packs', 'user'));
    }

    public function purchase(CreditPack $pack)
    {
        return view('owner.credits.purchase', compact('pack'));
    }

    public function processPurchase(Request $request, CreditPack $pack)
    {
        $user = auth()->user();
        
        // Record the purchase
        UserCreditPurchase::create([
            'user_id' => $user->id,
            'credit_pack_id' => $pack->id,
            'amount_paid' => $pack->price,
            'credits_purchased' => $pack->credits,
            'pack_snapshot' => $pack->toArray(),
        ]);

        // Add credits to user
        $user->increment('message_credits', $pack->credits);

        return redirect()->route('owner.credits.index')
            ->with('success', "Successfully purchased {$pack->credits} message credits!");
    }

    public function myPurchases()
    {
        $purchases = auth()->user()->creditPurchases()->with('pack')->latest()->get();
        return view('owner.credits.my-purchases', compact('purchases'));
    }
}