@extends('layouts.admin')

@section('title', 'Super Admin Dashboard')

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
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Welcome {{ auth()->user()->name }}</h3>
                    <h6 class="font-weight-normal mb-0">Super Admin Dashboard</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 mb-4 stretch-card transparent">
            <div class="card card-tale">
                <div class="card-body">
                    <p class="mb-4">Total Owners</p>
                    <p class="fs-30 mb-2">0</p>
                    <p>Active Accounts</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4 stretch-card transparent">
            <div class="card card-dark-blue">
                <div class="card-body">
                    <p class="mb-4">Total Properties</p>
                    <p class="fs-30 mb-2">0</p>
                    <p>Listed Properties</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4 stretch-card transparent">
            <div class="card card-light-blue">
                <div class="card-body">
                    <p class="mb-4">Total Tenants</p>
                    <p class="fs-30 mb-2">0</p>
                    <p>Active Tenants</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4 stretch-card transparent">
            <div class="card card-light-danger">
                <div class="card-body">
                    <p class="mb-4">Revenue</p>
                    <p class="fs-30 mb-2">$0</p>
                    <p>This Month</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="card-title mb-0">Recent Activities</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-borderless">
                            <thead>
                                <tr>
                                    <th>Owner</th>
                                    <th>Property</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">No activities yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
