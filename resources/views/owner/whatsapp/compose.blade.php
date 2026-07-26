@extends('layouts.admin')

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Send WhatsApp Message</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Tenant Details:</h6>
                            <p class="mb-1"><strong>Name:</strong> {{ $tenant->name }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $tenant->phone }}</p>
                            <p class="mb-0"><strong>Email:</strong> {{ $tenant->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Your Credits:</strong> {{ auth()->user()->message_credits }}<br>
                                    <strong>Cost:</strong> 1 credit per message
                                </small>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('owner.whatsapp.send-custom', $tenant) }}">
                        @csrf
                        <div class="form-group">
                            <label>Message *</label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" 
                                      rows="8" required placeholder="Type your message here...">{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Maximum 1000 characters</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-whatsapp"></i> Send Message (1 Credit)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection