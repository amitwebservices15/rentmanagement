@extends('layouts.admin')

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Subscription Plans</h2>
        <div class="text-right">
            <small class="text-muted">Your Credits: <strong>{{ auth()->user()->message_credits }}</strong></small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($currentSubscription)
        <div class="alert alert-info">
            <strong>Current Subscription:</strong> {{ $currentSubscription->plan->name }} 
            (Valid until {{ $currentSubscription->end_date->format('M d, Y') }})
        </div>
    @endif

    <div class="row">
        @foreach($plans as $plan)
        <div class="col-md-4 mb-4">
            <div class="card {{ $plan->is_popular ? 'border-warning' : '' }}">
                @if($plan->is_popular)
                    <div class="card-header bg-warning text-white text-center">
                        <strong>Most Popular</strong>
                    </div>
                @endif
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $plan->name }}</h4>
                    <h2 class="text-primary">₹{{ number_format($plan->price) }}</h2>
                    <p class="text-muted">{{ $plan->validity_days }} days validity</p>
                    
                    <ul class="list-unstyled mt-3">
                        <li><strong>{{ $plan->message_credits }}</strong> Message Credits</li>
                        <li><strong>{{ $plan->max_properties }}</strong> Properties</li>
                        <li><strong>{{ $plan->max_rooms }}</strong> Rooms per Property</li>
                        @if($plan->features)
                            @foreach($plan->features as $feature)
                                <li><i class="fas fa-check text-success"></i> {{ $feature }}</li>
                            @endforeach
                        @endif
                    </ul>
                    
                    <a href="{{ route('owner.subscriptions.purchase', $plan) }}" 
                       class="btn btn-{{ $plan->is_popular ? 'warning' : 'primary' }} btn-block">
                        Choose Plan
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Need More Credits?</h5>
                </div>
                <div class="card-body">
                    <p>Running low on message credits? Purchase additional credit packs anytime!</p>
                    <a href="{{ route('owner.credits.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> Buy Credit Packs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection