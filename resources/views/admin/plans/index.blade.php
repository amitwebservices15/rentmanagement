@extends('layouts.admin')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/admin/dashboard') }}">
            <i class="icon-grid menu-icon"></i>
            <span class="menu-title">Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="icon-head menu-icon"></i>
            <span class="menu-title">Manage Owners</span>
        </a>
    </li>
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('admin.plans.index') }}">
            <i class="icon-star menu-icon"></i>
            <span class="menu-title">Subscription Plans</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.credits.index') }}">
            <i class="icon-wallet menu-icon"></i>
            <span class="menu-title">Credit Packs</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="icon-columns menu-icon"></i>
            <span class="menu-title">Properties</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="icon-paper menu-icon"></i>
            <span class="menu-title">Reports</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="icon-settings menu-icon"></i>
            <span class="menu-title">Settings</span>
        </a>
    </li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Subscription Plans</h2>
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Plan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Credits</th>
                            <th>Validity</th>
                            <th>Properties</th>
                            <th>Rooms</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                        <tr>
                            <td>
                                {{ $plan->name }}
                                @if($plan->is_popular)
                                    <span class="badge badge-warning">Popular</span>
                                @endif
                            </td>
                            <td>₹{{ number_format($plan->price) }}</td>
                            <td>{{ $plan->message_credits }}</td>
                            <td>{{ $plan->validity_days }} days</td>
                            <td>{{ $plan->max_properties }}</td>
                            <td>{{ $plan->max_rooms }}</td>
                            <td>
                                <span class="badge badge-{{ $plan->is_active ? 'success' : 'secondary' }}">
                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Delete this plan?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No plans found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection