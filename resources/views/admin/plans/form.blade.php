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
        <h2>{{ $plan->exists ? 'Edit' : 'Create' }} Subscription Plan</h2>
        <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $plan->exists ? route('admin.plans.update', $plan) : route('admin.plans.store') }}">
                @csrf
                @if($plan->exists) @method('PUT') @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Plan Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $plan->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Price (₹) *</label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                   value="{{ old('price', $plan->price) }}" min="0" step="0.01" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Message Credits *</label>
                            <input type="number" name="message_credits" class="form-control @error('message_credits') is-invalid @enderror" 
                                   value="{{ old('message_credits', $plan->message_credits) }}" min="0" required>
                            @error('message_credits')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Validity (Days) *</label>
                            <input type="number" name="validity_days" class="form-control @error('validity_days') is-invalid @enderror" 
                                   value="{{ old('validity_days', $plan->validity_days) }}" min="1" required>
                            @error('validity_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Max Properties *</label>
                            <input type="number" name="max_properties" class="form-control @error('max_properties') is-invalid @enderror" 
                                   value="{{ old('max_properties', $plan->max_properties) }}" min="1" required>
                            @error('max_properties')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Max Rooms per Property *</label>
                            <input type="number" name="max_rooms" class="form-control @error('max_rooms') is-invalid @enderror" 
                                   value="{{ old('max_rooms', $plan->max_rooms) }}" min="1" required>
                            @error('max_rooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Features (one per line)</label>
                            <textarea name="features_raw" class="form-control" rows="4">{{ old('features_raw', is_array($plan->features) ? implode("\n", $plan->features) : '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" 
                                   {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_popular" value="1" class="form-check-input" 
                                   {{ old('is_popular', $plan->is_popular) ? 'checked' : '' }}>
                            <label class="form-check-label">Mark as Popular</label>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">
                        {{ $plan->exists ? 'Update' : 'Create' }} Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection