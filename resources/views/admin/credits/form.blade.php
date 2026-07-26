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
        <h2>{{ $pack->exists ? 'Edit' : 'Create' }} Credit Pack</h2>
        <a href="{{ route('admin.credits.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $pack->exists ? route('admin.credits.update', $pack) : route('admin.credits.store') }}">
                @csrf
                @if($pack->exists) @method('PUT') @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pack Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $pack->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Price (₹) *</label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                   value="{{ old('price', $pack->price) }}" min="0" step="0.01" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Message Credits *</label>
                            <input type="number" name="credits" class="form-control @error('credits') is-invalid @enderror" 
                                   value="{{ old('credits', $pack->credits) }}" min="1" required>
                            @error('credits')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tag (Optional)</label>
                            <input type="text" name="tag" class="form-control @error('tag') is-invalid @enderror" 
                                   value="{{ old('tag', $pack->tag) }}" placeholder="e.g., Best Value, Popular">
                            @error('tag')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" 
                           {{ old('is_active', $pack->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label">Active</label>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        {{ $pack->exists ? 'Update' : 'Create' }} Credit Pack
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection