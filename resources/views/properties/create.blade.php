@extends('layouts.admin')

@section('title', 'Add Property')

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 col-lg-8 grid-margin mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold mb-0">Add Property</h4>
            <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('properties.store') }}">
                    @csrf
                    @include('properties.form')
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="mdi mdi-content-save me-1"></i> Save Property
                        </button>
                        <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
