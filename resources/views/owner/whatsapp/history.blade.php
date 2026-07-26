@extends('layouts.admin')

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>WhatsApp Message History</h2>
        <div class="text-right">
            <small class="text-muted">Your Credits: <strong>{{ auth()->user()->message_credits }}</strong></small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Tenant</th>
                            <th>Phone</th>
                            <th>Message Type</th>
                            <th>Status</th>
                            <th>Credits Used</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                        <tr>
                            <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $message->tenant->name }}</td>
                            <td>{{ $message->phone_number }}</td>
                            <td>
                                @if($message->rent_record_id)
                                    @if(str_contains($message->message, 'RENT SLIP'))
                                        <span class="badge badge-primary">Rent Slip</span>
                                    @else
                                        <span class="badge badge-warning">Rent Reminder</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary">Custom Message</span>
                                @endif
                            </td>
                            <td>
                                @if($message->status === 'sent')
                                    <span class="badge badge-success">Sent</span>
                                @elseif($message->status === 'failed')
                                    <span class="badge badge-danger">Failed</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $message->credits_used }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick="showMessage({{ $message->id }}, '{{ addslashes($message->message) }}')">
                                    View Message
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No messages sent yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">WhatsApp Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="bg-light p-3 rounded" style="white-space: pre-wrap;" id="messageContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showMessage(id, message) {
    document.getElementById('messageContent').textContent = message;
    new bootstrap.Modal(document.getElementById('messageModal')).show();
}
</script>
@endsection