<?php

namespace App\Services;

use App\Models\WhatsAppMessage;
use App\Models\User;
use App\Models\Tenant;
use App\Models\RentRecord;

class WhatsAppService
{
    public function sendRentSlip(User $owner, Tenant $tenant, RentRecord $rentRecord)
    {
        // Check if owner has credits
        if ($owner->message_credits < 1) {
            throw new \Exception('Insufficient credits. Please purchase more credits to send messages.');
        }

        // Check if rent slip message already sent for this record
        $existingMessage = WhatsAppMessage::where('user_id', $owner->id)
            ->where('tenant_id', $tenant->id)
            ->where('rent_record_id', $rentRecord->id)
            ->where('status', 'sent')
            ->first();

        if ($existingMessage) {
            throw new \Exception('Rent slip has already been sent to this tenant for this month.');
        }

        // Prepare rent slip message
        $message = $this->prepareRentSlipMessage($tenant, $rentRecord);
        
        // Create message record
        $whatsappMessage = WhatsAppMessage::create([
            'user_id' => $owner->id,
            'tenant_id' => $tenant->id,
            'rent_record_id' => $rentRecord->id,
            'phone_number' => $tenant->phone,
            'message' => $message,
            'status' => 'pending',
            'credits_used' => 1,
        ]);

        // Deduct credit from owner
        $owner->decrement('message_credits', 1);

        // Send WhatsApp message (simulate for now)
        $success = $this->sendWhatsAppMessage($tenant->phone, $message);

        if ($success) {
            $whatsappMessage->update([
                'status' => 'sent',
                'sent_at' => now(),
                'whatsapp_message_id' => 'wa_' . uniqid(),
            ]);
        } else {
            $whatsappMessage->update(['status' => 'failed']);
            // Refund credit on failure
            $owner->increment('message_credits', 1);
            throw new \Exception('Failed to send WhatsApp message. Credit has been refunded.');
        }

        return $whatsappMessage;
    }

    public function sendRentReminder(User $owner, Tenant $tenant, RentRecord $rentRecord)
    {
        // Check if owner has credits
        if ($owner->message_credits < 1) {
            throw new \Exception('Insufficient credits. Please purchase more credits to send messages.');
        }

        // Check if reminder already sent for this record to this tenant
        $existingReminder = WhatsAppMessage::where('user_id', $owner->id)
            ->where('tenant_id', $tenant->id)
            ->where('rent_record_id', $rentRecord->id)
            ->whereIn('status', ['sent', 'pending'])
            ->where('message', 'LIKE', '%Rent Reminder%')
            ->first();

        if ($existingReminder) {
            throw new \Exception('Rent reminder has already been sent to this tenant for this month.');
        }

        // Prepare reminder message
        $message = $this->prepareRentReminderMessage($tenant, $rentRecord);
        
        // Create message record
        $whatsappMessage = WhatsAppMessage::create([
            'user_id' => $owner->id,
            'tenant_id' => $tenant->id,
            'rent_record_id' => $rentRecord->id,
            'phone_number' => $tenant->phone,
            'message' => $message,
            'status' => 'pending',
            'credits_used' => 1,
        ]);

        // Deduct credit from owner
        $owner->decrement('message_credits', 1);

        // Send WhatsApp message
        $success = $this->sendWhatsAppMessage($tenant->phone, $message);

        if ($success) {
            $whatsappMessage->update([
                'status' => 'sent',
                'sent_at' => now(),
                'whatsapp_message_id' => 'wa_' . uniqid(),
            ]);
        } else {
            $whatsappMessage->update(['status' => 'failed']);
            // Refund credit on failure
            $owner->increment('message_credits', 1);
            throw new \Exception('Failed to send WhatsApp message. Credit has been refunded.');
        }

        return $whatsappMessage;
    }
    
    public function sendCustomMessage(User $owner, Tenant $tenant, string $customMessage)
    {
        // Check if owner has credits
        if ($owner->message_credits < 1) {
            throw new \Exception('Insufficient credits. Please purchase more credits to send messages.');
        }

        // Create message record
        $whatsappMessage = WhatsAppMessage::create([
            'user_id' => $owner->id,
            'tenant_id' => $tenant->id,
            'phone_number' => $tenant->phone,
            'message' => $customMessage,
            'status' => 'pending',
            'credits_used' => 1,
        ]);

        // Deduct credit from owner
        $owner->decrement('message_credits', 1);

        // Send WhatsApp message
        $success = $this->sendWhatsAppMessage($tenant->phone, $customMessage);

        if ($success) {
            $whatsappMessage->update([
                'status' => 'sent',
                'sent_at' => now(),
                'whatsapp_message_id' => 'wa_' . uniqid(),
            ]);
        } else {
            $whatsappMessage->update(['status' => 'failed']);
            // Refund credit on failure
            $owner->increment('message_credits', 1);
            throw new \Exception('Failed to send WhatsApp message. Credit has been refunded.');
        }

        return $whatsappMessage;
    }

    private function prepareRentReminderMessage(Tenant $tenant, RentRecord $rentRecord)
    {
        $month = \Carbon\Carbon::createFromFormat('Y-m', $rentRecord->month)->format('F Y');
        $dueDate = $rentRecord->due_date ? $rentRecord->due_date->format('d M Y') : 'Not specified';
        $totalAmount = number_format($rentRecord->total_amount, 0);
        $dueAmount = number_format($rentRecord->due_amount, 0);
        
        return "🔔 *RENT REMINDER - {$month}*\n\n" .
               "Dear {$tenant->name},\n\n" .
               "This is a friendly reminder for your pending rent payment.\n\n" .
               "🏢 *Property:* {$rentRecord->room->property->name}\n" .
               "🚪 *Room:* {$rentRecord->room->room_number}\n" .
               "📅 *Month:* {$month}\n" .
               "📆 *Due Date:* {$dueDate}\n" .
               "💰 *Total Amount:* ₹{$totalAmount}\n" .
               "🔴 *Amount Due:* ₹{$dueAmount}\n\n" .
               "Please make the payment as soon as possible to avoid any inconvenience.\n\n" .
               "Thank you for your cooperation!\n" .
               "Property Management";
    }

    

    private function sendWhatsAppMessage(string $phoneNumber, string $message)
    {
        // For demo purposes, we'll simulate sending
        // In production, integrate with WhatsApp Business API or services like Twilio
        
        // Simulate API call delay
        usleep(500000); // 0.5 second delay
        
        // Simulate 95% success rate
        return rand(1, 100) <= 95;
        
        /* 
        // Example integration with Twilio WhatsApp API:
        
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        
        try {
            $message = $twilio->messages->create(
                "whatsapp:+{$phoneNumber}",
                [
                    'from' => 'whatsapp:' . config('services.twilio.whatsapp_number'),
                    'body' => $message
                ]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
        */
    }

    public function getMessageHistory(User $owner, $limit = 50)
    {
        return WhatsAppMessage::where('user_id', $owner->id)
            ->with(['tenant', 'rentRecord.room.property'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}