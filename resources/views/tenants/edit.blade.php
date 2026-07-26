@extends('layouts.admin')

@section('title', 'Edit Tenant')

@section('sidebar')
    @include('partials.owner-sidebar')
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 col-lg-8 grid-margin mx-auto">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold mb-0">Edit Tenant — <span class="text-primary">{{ $tenant->name }}</span></h4>
            <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('tenants.update', $tenant) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    @include('tenants.form')
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="mdi mdi-content-save me-1"></i> Update Tenant
                        </button>
                        <a href="{{ route('tenants.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
