@extends('layouts.admin')

@section('title', 'Add Room — ' . $property->property_name)

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 col-lg-8 grid-margin mx-auto">

        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('properties.index') }}">Properties</a></li>
                <li class="breadcrumb-item"><a href="{{ route('properties.rooms.index', $property) }}">{{ $property->property_name }}</a></li>
                <li class="breadcrumb-item active">Add Room</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold mb-0">Add Room</h4>
            <a href="{{ route('properties.rooms.index', $property) }}" class="btn btn-outline-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('properties.rooms.store', $property) }}">
                    @csrf
                    @include('rooms.form')
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="mdi mdi-content-save me-1"></i> Save Room
                        </button>
                        <a href="{{ route('properties.rooms.index', $property) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
