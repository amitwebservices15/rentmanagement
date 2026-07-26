@extends('layouts.admin')

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Confirm Subscription Purchase</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3>{{ $plan->name }}</h3>
                        <h2 class="text-primary">₹{{ number_format($plan->price) }}</h2>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <strong>Validity:</strong><br>
                            {{ $plan->validity_days }} days
                        </div>
                        <div class="col-6">
                            <strong>Message Credits:</strong><br>
                            {{ $plan->message_credits }} credits
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            <strong>Max Properties:</strong><br>
                            {{ $plan->max_properties }}
                        </div>
                        <div class="col-6">
                            <strong>Max Rooms:</strong><br>
                            {{ $plan->max_rooms }}
                        </div>
                    </div>

                    @if($plan->features)
                        <div class="mt-3">
                            <strong>Features:</strong>
                            <ul class="mt-2">
                                @foreach($plan->features as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            This will replace your current subscription if any. Credits will be added to your account immediately.
                        </small>
                    </div>

                    <form method="POST" action="{{ route('owner.subscriptions.process', $plan) }}">
                        @csrf
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('owner.subscriptions.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                Confirm Purchase - ₹{{ number_format($plan->price) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection