<li class="nav-item {{ request()->is('owner/dashboard') ? 'active' : '' }}">
    <a class="nav-link" href="{{ url('/owner/dashboard') }}">
        <i class="icon-grid menu-icon"></i>
        <span class="menu-title">Dashboard</span>
    </a>
</li>
<li class="nav-item {{ request()->is('subscriptions*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('owner.subscriptions.index') }}">
        <i class="icon-star menu-icon"></i>
        <span class="menu-title">Subscriptions</span>
    </a>
</li>
<li class="nav-item {{ request()->is('credits*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('owner.credits.index') }}">
        <i class="icon-wallet menu-icon"></i>
        <span class="menu-title">Credits</span>
        <span class="badge badge-primary ml-2">{{ auth()->user()->message_credits }}</span>
    </a>
</li>
<li class="nav-item {{ request()->is('properties*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('properties.index') }}">
        <i class="icon-layout menu-icon"></i>
        <span class="menu-title">My Properties</span>
    </a>
</li>
<li class="nav-item {{ request()->is('tenants*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('tenants.index') }}">
        <i class="icon-head menu-icon"></i>
        <span class="menu-title">Tenants</span>
    </a>
</li>
<li class="nav-item {{ request()->is('whatsapp*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('owner.whatsapp.history') }}">
        <i class="mdi mdi-whatsapp menu-icon"></i>
        <span class="menu-title">WhatsApp Messages</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="#">
        <i class="icon-paper menu-icon"></i>
        <span class="menu-title">Payments</span>
    </a>
</li>
