@extends('layouts.admin')

@section('title', 'Generate Rent — Room ' . $room->room_number)

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-lg-7 col-xl-6 grid-margin mx-auto">

        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('properties.index') }}">Properties</a></li>
                <li class="breadcrumb-item"><a href="{{ route('properties.rooms.index', $property) }}">{{ $property->property_name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('rent.index', [$property, $room]) }}">Room {{ $room->room_number }} Bills</a></li>
                <li class="breadcrumb-item active">Generate</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="font-weight-bold mb-0">Generate Rent Bill</h4>
                <small class="text-muted">Room {{ $room->room_number }} · {{ $property->property_name }}</small>
            </div>
            <a href="{{ route('rent.index', [$property, $room]) }}" class="btn btn-outline-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
        </div>

        @if($assignments->isEmpty())
            <div class="alert alert-warning">
                <i class="mdi mdi-alert me-1"></i>
                No active tenants in this room. Assign tenants first.
            </div>
        @elseif($alreadyBilled)
            <div class="alert alert-warning">
                <i class="mdi mdi-alert-circle me-1"></i>
                Bill already generated for this room for <strong>{{ now()->format('F Y') }}</strong>.
                <a href="{{ route('rent.index', [$property, $room]) }}" class="alert-link">View Bills</a>
            </div>
        @else

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('rent.store', [$property, $room]) }}">
            @csrf

            {{-- Tenants info --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="small text-muted font-weight-bold">
                            <i class="mdi mdi-account-group me-1"></i>Tenants:
                        </span>
                        @foreach($assignments as $a)
                            <span class="badge badge-info">{{ $a->tenant->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Billing Period --}}
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3">
                        <i class="mdi mdi-calendar me-1 text-primary"></i> Billing Period
                    </h6>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group mb-0">
                                <label class="small">Billing Month <span class="text-danger">*</span></label>
                                <input type="month" class="form-control form-control-sm @error('month') is-invalid @enderror"
                                       name="month" value="{{ old('month', $month) }}" required>
                                @error('month') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group mb-0">
                                <label class="small">Due Date</label>
                                <input type="date" class="form-control form-control-sm"
                                       name="due_date"
                                       value="{{ old('due_date', now()->addDays(10)->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- All Charges --}}
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3">
                        <i class="mdi mdi-receipt me-1 text-primary"></i> Charges
                    </h6>

                    {{-- Rent --}}
                    <div class="form-group">
                        <label class="small">Rent (₹) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm"
                               id="rent_amount" name="rent_amount"
                               value="{{ old('rent_amount', $room->rent_amount) }}"
                               min="0" step="0.01" required oninput="recalcTotal()">
                    </div>

                    {{-- Meter Reading --}}
                    <div class="bg-light rounded p-3 mb-3">
                        <p class="small font-weight-bold text-muted mb-2">
                            <i class="mdi mdi-gauge me-1"></i> Electricity Meter Reading
                        </p>
                        <div class="row">
                            <div class="col-6 col-sm-3">
                                <div class="form-group mb-0">
                                    <label class="small">Previous</label>
                                    <input type="number" class="form-control form-control-sm"
                                           id="meter_start" name="meter_start"
                                           value="{{ old('meter_start', $lastMeterReading) }}"
                                           min="0" step="0.01" readonly
                                           style="background:#f8f9fa!important;">
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="form-group mb-0">
                                    <label class="small">Current</label>
                                    <input type="number" class="form-control form-control-sm"
                                           id="meter_end" name="meter_end"
                                           value="{{ old('meter_end') }}"
                                           min="0" step="0.01" oninput="calcUnits()">
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="form-group mb-0">
                                    <label class="small">Units</label>
                                    <input type="number" class="form-control form-control-sm"
                                           id="electricity_units" name="electricity_units"
                                           value="{{ old('electricity_units', 0) }}"
                                           min="0" step="0.01" oninput="calcElec()">
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="form-group mb-0">
                                    <label class="small">Rate/Unit (₹)</label>
                                    <input type="number" class="form-control form-control-sm"
                                           id="rate_per_unit" name="rate_per_unit"
                                           value="{{ old('rate_per_unit', 0) }}"
                                           min="0" step="0.01" oninput="calcElec()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="small">Electricity Charge (₹)</label>
                                <input type="number" class="form-control form-control-sm"
                                       id="electricity_charge" name="electricity_charge"
                                       value="{{ old('electricity_charge', 0) }}"
                                       min="0" step="0.01" oninput="recalcTotal()">
                                <small class="text-muted" style="font-size:10px;">Auto-filled or enter manually</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="small">Other Charges (₹)</label>
                                <input type="number" class="form-control form-control-sm"
                                       id="other_charges" name="other_charges"
                                       value="{{ old('other_charges', 0) }}"
                                       min="0" step="0.01" oninput="recalcTotal()">
                                <small class="text-muted" style="font-size:10px;">Maintenance, water, etc.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Previous Due / Advance carry-forward --}}
                    @if($previousDue > 0 || $advanceAmount > 0)
                    <div class="alert {{ $previousDue > 0 ? 'alert-danger' : 'alert-success' }} py-2 mb-3">
                        @if($previousDue > 0)
                            <i class="mdi mdi-alert-circle me-1"></i>
                            <strong>Previous Due: ₹{{ number_format($previousDue, 2) }}</strong>
                            — added to this bill.
                        @else
                            <i class="mdi mdi-check-circle me-1"></i>
                            <strong>Advance: ₹{{ number_format($advanceAmount, 2) }}</strong>
                            — deducted from this bill.
                        @endif
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="small text-danger">
                                    <i class="mdi mdi-arrow-up-circle me-1"></i>Previous Due (₹)
                                </label>
                                <input type="number" class="form-control form-control-sm border-danger"
                                       id="previous_due" name="previous_due"
                                       value="{{ old('previous_due', $previousDue) }}"
                                       min="0" step="0.01" oninput="recalcTotal()">
                                <small class="text-muted" style="font-size:10px;">Added to total bill</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="small text-success">
                                    <i class="mdi mdi-arrow-down-circle me-1"></i>Advance (₹)
                                </label>
                                <input type="number" class="form-control form-control-sm border-success"
                                       id="advance_amount" name="advance_amount"
                                       value="{{ old('advance_amount', $advanceAmount) }}"
                                       min="0" step="0.01" oninput="recalcTotal()">
                                <small class="text-muted" style="font-size:10px;">Deducted from total bill</small>
                            </div>
                        </div>
                    </div>

                    {{-- Bill breakdown + Total --}}
                    <div class="border rounded p-3 mb-0">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Rent</span>
                            <span id="breakdown_rent">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Electricity</span>
                            <span id="breakdown_elec">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Other</span>
                            <span id="breakdown_other">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1 text-danger" id="row_prev_due" style="display:none!important;">
                            <span>+ Previous Due</span>
                            <span id="breakdown_prev_due">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2 text-success" id="row_advance" style="display:none!important;">
                            <span>− Advance</span>
                            <span id="breakdown_advance">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between font-weight-bold border-top pt-2">
                            <span>Total Bill</span>
                            <span class="text-primary" id="grand_total">₹0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- WhatsApp Notification Option --}}
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3">
                        <i class="mdi mdi-whatsapp me-1 text-success"></i> WhatsApp Notification
                    </h6>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="send_whatsapp" name="send_whatsapp" value="1" 
                               {{ old('send_whatsapp', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="send_whatsapp">
                            <strong>Send rent slip to tenants via WhatsApp</strong>
                        </label>
                    </div>
                    
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="mdi mdi-information-outline me-1"></i>
                            This will send a detailed rent slip to all active tenants in this room.
                            <strong>Cost:</strong> 1 credit per tenant
                        </small>
                    </div>
                    
                    <div class="mt-2">
                        <small class="text-info">
                            <i class="mdi mdi-account-group me-1"></i>
                            <strong>Recipients:</strong> 
                            @foreach($assignments as $assignment)
                                {{ $assignment->tenant->name }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                            ({{ $assignments->count() }} tenant{{ $assignments->count() > 1 ? 's' : '' }})
                        </small>
                    </div>
                    
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="mdi mdi-wallet me-1"></i>
                            <strong>Your Credits:</strong> {{ auth()->user()->message_credits }}
                            @if(auth()->user()->message_credits < $assignments->count())
                                <span class="text-danger">
                                    (Insufficient credits - need {{ $assignments->count() }} credits)
                                </span>
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="mdi mdi-receipt me-1"></i> Generate Bill
                </button>
                <a href="{{ route('rent.index', [$property, $room]) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>

        </form>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
function calcUnits() {
    const start = parseFloat(document.getElementById('meter_start').value) || 0;
    const end   = parseFloat(document.getElementById('meter_end').value)   || 0;
    document.getElementById('electricity_units').value = Math.max(0, end - start).toFixed(2);
    calcElec();
}
function calcElec() {
    const units = parseFloat(document.getElementById('electricity_units').value) || 0;
    const rate  = parseFloat(document.getElementById('rate_per_unit').value)     || 0;
    if (rate > 0) document.getElementById('electricity_charge').value = (units * rate).toFixed(2);
    recalcTotal();
}
function recalcTotal() {
    const rent    = parseFloat(document.getElementById('rent_amount').value)        || 0;
    const elec    = parseFloat(document.getElementById('electricity_charge').value) || 0;
    const other   = parseFloat(document.getElementById('other_charges').value)      || 0;
    const prevDue = parseFloat(document.getElementById('previous_due').value)       || 0;
    const advance = parseFloat(document.getElementById('advance_amount').value)     || 0;
    const total   = rent + elec + other + prevDue - advance;

    document.getElementById('breakdown_rent').textContent  = '₹' + rent.toFixed(2);
    document.getElementById('breakdown_elec').textContent  = '₹' + elec.toFixed(2);
    document.getElementById('breakdown_other').textContent = '₹' + other.toFixed(2);
    document.getElementById('grand_total').textContent     = '₹' + total.toFixed(2);

    const rowPrev = document.getElementById('row_prev_due');
    const rowAdv  = document.getElementById('row_advance');

    if (prevDue > 0) {
        rowPrev.style.removeProperty('display');
        document.getElementById('breakdown_prev_due').textContent = '₹' + prevDue.toFixed(2);
    } else {
        rowPrev.style.display = 'none';
    }
    if (advance > 0) {
        rowAdv.style.removeProperty('display');
        document.getElementById('breakdown_advance').textContent = '₹' + advance.toFixed(2);
    } else {
        rowAdv.style.display = 'none';
    }
}

// Check credit availability and update WhatsApp option
function checkWhatsAppOption() {
    const userCredits = {{ auth()->user()->message_credits }};
    const requiredCredits = {{ $assignments->count() }};
    const checkbox = document.getElementById('send_whatsapp');
    
    if (userCredits < requiredCredits) {
        checkbox.checked = false;
        checkbox.disabled = true;
        
        // Show warning message
        const warningDiv = document.createElement('div');
        warningDiv.className = 'alert alert-warning mt-2';
        warningDiv.innerHTML = '<i class="mdi mdi-alert me-1"></i><strong>Insufficient Credits:</strong> You need ' + requiredCredits + ' credits but have only ' + userCredits + '. <a href="{{ route('owner.credits.index') }}">Purchase more credits</a>';
        
        const whatsappCard = checkbox.closest('.card-body');
        if (!whatsappCard.querySelector('.alert-warning')) {
            whatsappCard.appendChild(warningDiv);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    recalcTotal();
    checkWhatsAppOption();
});
</script>
@endpush
