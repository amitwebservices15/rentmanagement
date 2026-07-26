@extends('layouts.admin')

@section('title', 'Bills — Room ' . $room->room_number)

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin">

        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('properties.index') }}">Properties</a></li>
                <li class="breadcrumb-item"><a href="{{ route('properties.rooms.index', $property) }}">{{ $property->property_name }}</a></li>
                <li class="breadcrumb-item active">Room {{ $room->room_number }} — Bills</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="font-weight-bold mb-0">Bills — Room {{ $room->room_number }}</h4>
                <small class="text-muted">{{ $property->property_name }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('rent.create', [$property, $room]) }}" class="btn btn-success btn-sm">
                    <i class="mdi mdi-plus me-1"></i> Generate Bill
                </a>
                <a href="{{ route('properties.rooms.index', $property) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($records->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5 text-muted">
                    <i class="mdi mdi-receipt-outline" style="font-size:3rem;"></i>
                    <p class="mt-2 mb-3">No bills generated yet for this room.</p>
                    <a href="{{ route('rent.create', [$property, $room]) }}" class="btn btn-success btn-sm">
                        <i class="mdi mdi-plus me-1"></i> Generate First Bill
                    </a>
                </div>
            </div>
        @else

        {{-- Summary stats --}}
        <div class="row mb-4">
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-tale py-3 px-3">
                    <p class="mb-1 small">Total Billed</p>
                    <h5 class="mb-0 font-weight-bold">₹{{ number_format($records->sum('total_amount'), 0) }}</h5>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-dark-blue py-3 px-3">
                    <p class="mb-1 small">Total Paid</p>
                    <h5 class="mb-0 font-weight-bold">₹{{ number_format($records->sum('paid_amount'), 0) }}</h5>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-light-danger py-3 px-3">
                    <p class="mb-1 small">Total Due</p>
                    <h5 class="mb-0 font-weight-bold">₹{{ number_format($records->sum('due_amount'), 0) }}</h5>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-light-blue py-3 px-3">
                    <p class="mb-1 small">Months Billed</p>
                    <h5 class="mb-0 font-weight-bold">{{ $records->count() }}</h5>
                </div>
            </div>
        </div>

        {{-- Desktop: DataTable — one row per month --}}
        <div class="card d-none d-md-block">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="billsTable" class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th>Tenants</th>
                                <th>Rent</th>
                                <th>Electricity</th>
                                <th>Other</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                            <tr>
                                <td class="align-middle font-weight-bold" style="white-space:nowrap;">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $record->month)->format('M Y') }}
                                    @if($record->due_date)
                                        <br><small class="text-muted fw-normal">Due: {{ $record->due_date->format('d M') }}</small>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    {{ $record->tenant_names ?? '—' }}
                                </td>
                                <td class="align-middle">₹{{ number_format($record->rent_amount, 0) }}</td>
                                <td class="align-middle">
                                    ₹{{ number_format($record->electricity_charge, 0) }}
                                    @if($record->electricity_units > 0)
                                        <br><small class="text-muted">{{ $record->electricity_units }} units</small>
                                    @endif
                                    @if($record->meter_end > 0)
                                        <br><small class="text-muted">{{ $record->meter_start }}→{{ $record->meter_end }}</small>
                                    @endif
                                </td>
                                <td class="align-middle">₹{{ number_format($record->other_charges, 0) }}</td>
                                <td class="align-middle font-weight-bold text-primary">
                                    ₹{{ number_format($record->total_amount, 0) }}
                                </td>
                                <td class="align-middle text-success font-weight-bold">
                                    ₹{{ number_format($record->paid_amount, 0) }}
                                </td>
                                <td class="align-middle font-weight-bold {{ $record->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                    ₹{{ number_format($record->due_amount, 0) }}
                                </td>
                                <td class="align-middle">
                                    @if($record->status === 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @elseif($record->status === 'partial')
                                        <span class="badge badge-warning">Partial</span>
                                    @else
                                        <span class="badge badge-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($record->status !== 'paid')
                                        <button class="btn btn-sm btn-success me-1"
                                                onclick="openPayModal({{ $record->id }}, {{ $record->total_amount }}, {{ $record->paid_amount }})"
                                                title="Record Payment">
                                            <i class="mdi mdi-cash"></i>
                                        </button>
                                    @endif
                                    
                                    {{-- Send Rent Slip Button --}}
                                    <form action="{{ route('owner.whatsapp.rent-slip', $record) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-primary me-1" title="Send Rent Slip to All Tenants">
                                            <i class="mdi mdi-receipt"></i>
                                        </button>
                                    </form>
                                    
                                    {{-- Send Reminder Button (only for unpaid) --}}
                                    @if($record->due_amount > 0)
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown" title="Send Reminder">
                                                <i class="mdi mdi-bell"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @foreach($record->room->assignments()->where('status', 'active')->with('tenant')->get() as $assignment)
                                                    @if($assignment->tenant->phone)
                                                        <li>
                                                            <form action="{{ route('owner.whatsapp.rent-reminder', [$record, $assignment->tenant]) }}" method="POST">
                                                                @csrf
                                                                <button class="dropdown-item" type="submit">
                                                                    <i class="mdi mdi-account"></i> {{ $assignment->tenant->name }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <form action="{{ route('rent.destroy', $record) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Delete this bill?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Mobile: one card per month --}}
        <div class="d-md-none">
            @foreach($records as $record)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center py-2"
                     style="background:#f8f9fa;">
                    <h6 class="font-weight-bold mb-0">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $record->month)->format('F Y') }}
                    </h6>
                    @if($record->status === 'paid')
                        <span class="badge badge-success">Paid</span>
                    @elseif($record->status === 'partial')
                        <span class="badge badge-warning">Partial</span>
                    @else
                        <span class="badge badge-danger">Unpaid</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($record->tenant_names)
                        <p class="small mb-2">
                            <i class="mdi mdi-account-group me-1 text-muted"></i>
                            {{ $record->tenant_names }}
                        </p>
                    @endif
                    <div class="row text-center mb-3">
                        <div class="col-3">
                            <div class="text-muted" style="font-size:10px;">Rent</div>
                            <div class="small font-weight-bold">₹{{ number_format($record->rent_amount, 0) }}</div>
                        </div>
                        <div class="col-3">
                            <div class="text-muted" style="font-size:10px;">Electricity</div>
                            <div class="small font-weight-bold">₹{{ number_format($record->electricity_charge, 0) }}</div>
                        </div>
                        <div class="col-3">
                            <div class="text-muted" style="font-size:10px;">Other</div>
                            <div class="small font-weight-bold">₹{{ number_format($record->other_charges, 0) }}</div>
                        </div>
                        <div class="col-3">
                            <div class="text-muted" style="font-size:10px;">Total</div>
                            <div class="small font-weight-bold text-primary">₹{{ number_format($record->total_amount, 0) }}</div>
                        </div>
                    </div>
                    @if($record->previous_due > 0)
                        <p class="small text-danger mb-1">
                            <i class="mdi mdi-arrow-up-circle me-1"></i>
                            Previous Due Added: ₹{{ number_format($record->previous_due, 0) }}
                        </p>
                    @endif
                    @if($record->advance_amount > 0)
                        <p class="small text-success mb-1">
                            <i class="mdi mdi-arrow-down-circle me-1"></i>
                            Advance Deducted: ₹{{ number_format($record->advance_amount, 0) }}
                        </p>
                    @endif
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="small text-muted">Paid: </span>
                            <span class="small font-weight-bold text-success">₹{{ number_format($record->paid_amount, 0) }}</span>
                            <span class="small text-muted ms-2">Due: </span>
                            <span class="small font-weight-bold {{ $record->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                ₹{{ number_format($record->due_amount, 0) }}
                            </span>
                        </div>
                        <div class="d-flex gap-1">
                            @if($record->status !== 'paid')
                                <button class="btn btn-sm btn-success"
                                        onclick="openPayModal({{ $record->id }}, {{ $record->total_amount }}, {{ $record->paid_amount }})">
                                    <i class="mdi mdi-cash"></i>
                                </button>
                            @endif
                            
                            {{-- Send Rent Slip Button --}}
                            <form action="{{ route('owner.whatsapp.rent-slip', $record) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-primary" title="Send Rent Slip">
                                    <i class="mdi mdi-receipt"></i>
                                </button>
                            </form>
                            
                            {{-- Send Reminder Dropdown --}}
                            @if($record->due_amount > 0)
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="mdi mdi-bell"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach($record->room->assignments()->where('status', 'active')->with('tenant')->get() as $assignment)
                                            @if($assignment->tenant->phone)
                                                <li>
                                                    <form action="{{ route('owner.whatsapp.rent-reminder', [$record, $assignment->tenant]) }}" method="POST">
                                                        @csrf
                                                        <button class="dropdown-item" type="submit">
                                                            {{ $assignment->tenant->name }}
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <form action="{{ route('rent.destroy', $record) }}" method="POST"
                                  onsubmit="return confirm('Delete this bill?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @endif
    </div>
</div>

{{-- Pay Modal --}}
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title font-weight-bold">Record Payment</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="payForm">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="small text-muted">Total Bill</span>
                        <span class="font-weight-bold" id="pay_total_display"></span>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small">Amount Paid (₹) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="paid_amount"
                               id="pay_amount" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="mdi mdi-check me-1"></i> Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css') }}">
<script src="{{ asset('assets/vendors/datatables.net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js') }}"></script>
<script>
$(document).ready(function () {
    $('#billsTable').DataTable({
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: [1, 11] }]
    });
});

function openPayModal(id, total, alreadyPaid) {
    document.getElementById('payForm').action = "{{ url('rent') }}/" + id + "/pay";
    document.getElementById('pay_total_display').textContent = '₹' + parseFloat(total).toFixed(2);
    document.getElementById('pay_amount').value = Math.max(0, parseFloat(total) - parseFloat(alreadyPaid)).toFixed(2);
    new bootstrap.Modal(document.getElementById('payModal')).show();
}
</script>
@endpush
