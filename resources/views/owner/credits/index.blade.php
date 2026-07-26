@extends('layouts.admin')

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Credit Packs</h2>
        <div class="text-right">
            <small class="text-muted">Your Credits: <strong>{{ $user->message_credits }}</strong></small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Need more message credits?</strong> Purchase credit packs to top up your account anytime.
    </div>

    <div class="row">
        @foreach($packs as $pack)
        <div class="col-md-3 mb-4">
            <div class="card">
                @if($pack->tag)
                    <div class="card-header bg-info text-white text-center">
                        <strong>{{ $pack->tag }}</strong>
                    </div>
                @endif
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $pack->name }}</h4>
                    <h2 class="text-success">₹{{ number_format($pack->price) }}</h2>
                    <h3 class="text-primary">{{ $pack->credits }} Credits</h3>
                    <p class="text-muted">₹{{ number_format($pack->price / $pack->credits, 2) }} per credit</p>
                    
                    <a href="{{ route('owner.credits.purchase', $pack) }}" 
                       class="btn btn-success btn-block">
                        Purchase Now
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
                    <h5>How Credits Work</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6><i class="fas fa-sms text-primary"></i> SMS Notifications</h6>
                            <p class="small">Send rent reminders and notifications to tenants</p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-envelope text-primary"></i> Email Alerts</h6>
                            <p class="small">Automated email notifications for rent due dates</p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-bell text-primary"></i> Push Notifications</h6>
                            <p class="small">Real-time alerts for important updates</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection