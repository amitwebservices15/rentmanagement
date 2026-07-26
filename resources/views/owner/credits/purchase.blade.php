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
                    <h4>Confirm Credit Purchase</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3>{{ $pack->name }}</h3>
                        @if($pack->tag)
                            <span class="badge badge-info">{{ $pack->tag }}</span>
                        @endif
                    </div>

                    <div class="row text-center">
                        <div class="col-6">
                            <h2 class="text-success">₹{{ number_format($pack->price) }}</h2>
                            <small class="text-muted">Total Price</small>
                        </div>
                        <div class="col-6">
                            <h2 class="text-primary">{{ $pack->credits }}</h2>
                            <small class="text-muted">Message Credits</small>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <p class="text-muted">
                            <strong>Cost per credit:</strong> ₹{{ number_format($pack->price / $pack->credits, 2) }}
                        </p>
                    </div>

                    <div class="alert alert-success mt-3">
                        <small>
                            <i class="fas fa-check-circle"></i>
                            Credits will be added to your account immediately after purchase.
                        </small>
                    </div>

                    <form method="POST" action="{{ route('owner.credits.process', $pack) }}">
                        @csrf
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('owner.credits.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                Confirm Purchase - ₹{{ number_format($pack->price) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection