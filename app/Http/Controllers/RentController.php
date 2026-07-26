<?php

namespace App\Http\Controllers;

use App\Models\RentRecord;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class RentController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }
    private function getRoom(int $propertyId, int $roomId): array
    {
        $property = auth()->user()->properties()->findOrFail($propertyId);
        $room     = $property->rooms()->findOrFail($roomId);
        return [$property, $room];
    }

    public function index(int $propertyId, int $roomId)
    {
        [$property, $room] = $this->getRoom($propertyId, $roomId);

        $records = RentRecord::where('room_id', $room->id)
            ->orderByDesc('month')
            ->get();

        return view('rent.index', compact('property', 'room', 'records'));
    }

    public function create(int $propertyId, int $roomId)
    {
        [$property, $room] = $this->getRoom($propertyId, $roomId);

        $month = now()->format('Y-m');

        $assignments = $room->assignments()
            ->with('tenant')
            ->where('status', 'active')
            ->get();

        $lastMeterReading = $assignments->max('electricity_meter_start') ?? 0;

        $alreadyBilled = RentRecord::where('room_id', $room->id)
            ->where('month', $month)
            ->exists();

        // Get previous month's balance (due or advance)
        $previousRecord = RentRecord::where('room_id', $room->id)
            ->where('month', '<', $month)
            ->orderByDesc('month')
            ->first();

        $previousDue     = 0;
        $advanceAmount   = 0;

        if ($previousRecord) {
            if ($previousRecord->due_amount > 0) {
                $previousDue = $previousRecord->due_amount;
            } elseif ($previousRecord->paid_amount > $previousRecord->total_amount) {
                $advanceAmount = $previousRecord->paid_amount - $previousRecord->total_amount;
            }
        }

        return view('rent.create', compact(
            'property', 'room', 'month', 'assignments',
            'lastMeterReading', 'alreadyBilled', 'previousDue', 'advanceAmount'
        ));
    }

    public function store(Request $request, int $propertyId, int $roomId)
    {
        [$property, $room] = $this->getRoom($propertyId, $roomId);

        $request->validate([
            'month'              => 'required|date_format:Y-m',
            'due_date'           => 'nullable|date',
            'rent_amount'        => 'required|numeric|min:0',
            'meter_start'        => 'nullable|numeric|min:0',
            'meter_end'          => 'nullable|numeric|min:0',
            'electricity_units'  => 'nullable|numeric|min:0',
            'rate_per_unit'      => 'nullable|numeric|min:0',
            'electricity_charge' => 'nullable|numeric|min:0',
            'other_charges'      => 'nullable|numeric|min:0',
            'previous_due'       => 'nullable|numeric|min:0',
            'advance_amount'     => 'nullable|numeric|min:0',
            'send_whatsapp'      => 'boolean',
        ]);

        if (RentRecord::where('room_id', $room->id)->where('month', $request->month)->exists()) {
            return back()->withErrors(['month' => 'Bill already generated for this room for ' . $request->month . '.']);
        }

        $rent         = (float) $request->rent_amount;
        $elecUnits    = (float) ($request->electricity_units  ?? 0);
        $elecCharge   = (float) ($request->electricity_charge ?? 0);
        $other        = (float) ($request->other_charges      ?? 0);
        $previousDue  = (float) ($request->previous_due       ?? 0);
        $advance      = (float) ($request->advance_amount     ?? 0);
        $meterStart   = (float) ($request->meter_start        ?? 0);
        $meterEnd     = $request->meter_end ? (float) $request->meter_end : 0;

        // Total = Rent + Elec + Other + Previous Due - Advance
        $total = $rent + $elecCharge + $other + $previousDue - $advance;

        $tenantNames = $room->assignments()
            ->with('tenant')
            ->where('status', 'active')
            ->get()
            ->pluck('tenant.name')
            ->join(', ');

        if ($meterEnd > 0) {
            $room->assignments()
                ->where('status', 'active')
                ->update(['electricity_meter_end' => $meterEnd]);
        }

        RentRecord::create([
            'room_id'            => $room->id,
            'property_id'        => $property->id,
            'tenant_id'          => null,
            'month'              => $request->month,
            'rent_amount'        => $rent,
            'electricity_units'  => $elecUnits,
            'electricity_charge' => $elecCharge,
            'other_charges'      => $other,
            'previous_due'       => $previousDue,
            'advance_amount'     => $advance,
            'meter_start'        => $meterStart,
            'meter_end'          => $meterEnd,
            'tenant_names'       => $tenantNames ?: null,
            'total_amount'       => $total,
            'paid_amount'        => 0,
            'due_amount'         => $total,
            'status'             => 'unpaid',
            'due_date'           => $request->due_date ?: null,
        ]);

        $successMessage = "Bill generated for Room {$room->room_number} — {$request->month}.";
        
        // Send WhatsApp notifications if requested
        if ($request->send_whatsapp) {
            $rentRecord = RentRecord::where('room_id', $room->id)
                ->where('month', $request->month)
                ->first();
                
            $activeTenants = $room->assignments()
                ->where('status', 'active')
                ->with('tenant')
                ->get()
                ->pluck('tenant');
                
            $sentCount = 0;
            $errors = [];
            $owner = auth()->user();
            
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
            
            if ($sentCount > 0) {
                $successMessage .= " WhatsApp rent slip sent to {$sentCount} tenant(s).";
            }
            
            if (!empty($errors)) {
                $successMessage .= " Errors: " . implode(', ', $errors);
            }
        }

        return redirect()->route('rent.index', [$property, $room])
            ->with('success', $successMessage);
    }

    public function markPaid(Request $request, RentRecord $record)
    {
        abort_if($record->property->owner_id !== auth()->id(), 403);

        $request->validate(['paid_amount' => 'required|numeric|min:0']);

        $paid   = (float) $request->paid_amount;
        $due    = max(0, $record->total_amount - $paid);
        $status = $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');

        $record->update([
            'paid_amount' => $paid,
            'due_amount'  => $due,
            'status'      => $status,
        ]);

        return back()->with('success', 'Payment recorded.');
    }

    public function destroy(RentRecord $record)
    {
        abort_if($record->property->owner_id !== auth()->id(), 403);
        $record->delete();
        return back()->with('success', 'Bill deleted.');
    }
}
