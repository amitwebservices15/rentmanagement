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
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.plans.index') }}">
            <i class="icon-star menu-icon"></i>
            <span class="menu-title">Subscription Plans</span>
        </a>
    </li>
    <li class="nav-item active">
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
        <h2>Credit Packs</h2>
        <a href="{{ route('admin.credits.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Credit Pack
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
                            <th>Tag</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packs as $pack)
                        <tr>
                            <td>{{ $pack->name }}</td>
                            <td>₹{{ number_format($pack->price) }}</td>
                            <td>{{ $pack->credits }}</td>
                            <td>
                                @if($pack->tag)
                                    <span class="badge badge-info">{{ $pack->tag }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $pack->is_active ? 'success' : 'secondary' }}">
                                    {{ $pack->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.credits.edit', $pack) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.credits.destroy', $pack) }}" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Delete this credit pack?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No credit packs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection