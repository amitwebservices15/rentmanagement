<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\RentRecord;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function sendRentSlip(Request $request, RentRecord $rentRecord)
    {
        try {
            $owner = auth()->user();
            
            // Get all active tenants for this room
            $activeTenants = $rentRecord->room->assignments()
                ->where('status', 'active')
                ->with('tenant')
                ->get()
                ->pluck('tenant');

            if ($activeTenants->isEmpty()) {
                return back()->with('error', 'No active tenants found for this room.');
            }

            $sentCount = 0;
            $errors = [];

            foreach ($activeTenants as $tenant) {
                if (!$tenant->phone) {
                    $errors[] = "No phone number for {$tenant->name}";
                    continue;
                }

                try {
                    $this->whatsappService->sendRentSlip($owner, $tenant, $rentRecord);
                    $sentCount++;
                } catch (\Exception $e) {
                    $errors[] = "{$tenant->name}: {$e->getMessage()}";
                }
            }

            $message = "Rent slip sent to {$sentCount} tenant(s).";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return back()->with($sentCount > 0 ? 'success' : 'error', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function sendRentReminder(Request $request, RentRecord $rentRecord, Tenant $tenant)
    {
        try {
            $owner = auth()->user();

            // Check if tenant has phone number
            if (!$tenant->phone) {
                return back()->with('error', 'Tenant phone number is not available.');
            }

            $message = $this->whatsappService->sendRentReminder($owner, $tenant, $rentRecord);

            return back()->with('success', 'WhatsApp reminder sent successfully! 1 credit used.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    public function sendCustomMessage(Request $request, Tenant $tenant)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $owner = auth()->user();

            // Check if tenant has phone number
            if (!$tenant->phone) {
                return back()->with('error', 'Tenant phone number is not available.');
            }

            $message = $this->whatsappService->sendCustomMessage($owner, $tenant, $request->message);

            return back()->with('success', 'WhatsApp message sent successfully! 1 credit used.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function messageHistory()
    {
        $owner = auth()->user();
        $messages = $this->whatsappService->getMessageHistory($owner);

        return view('owner.whatsapp.history', compact('messages'));
    }

    public function composeMessage(Tenant $tenant)
    {
        return view('owner.whatsapp.compose', compact('tenant'));
    }
}