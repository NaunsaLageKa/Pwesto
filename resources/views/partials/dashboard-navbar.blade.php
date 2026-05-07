<header class="dashboard-navbar">
    <div class="dashboard-navbar-left">
        @if(Auth::check() && Auth::user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="dashboard-admin-btn">Admin Panel</a>
        @endif
        <div class="dashboard-brand">PWESTO!</div>
    </div>

    <nav class="dashboard-navbar-right">
        <a href="{{ route('dashboard') }}" class="dash-link {{ ($active ?? '') === 'home' ? 'active' : '' }}">Home</a>
        <a href="{{ route('booking-history') }}" class="dash-link {{ ($active ?? '') === 'booking-history' ? 'active' : '' }}">Booking History</a>
        <a href="{{ route('services.index') }}" class="dash-link {{ ($active ?? '') === 'services' ? 'active' : '' }}">Services</a>
        <a href="{{ route('about') }}" class="dash-link {{ ($active ?? '') === 'about' ? 'active' : '' }}">About</a>
        <a href="{{ route('location') }}" class="dash-link {{ ($active ?? '') === 'location' ? 'active' : '' }}">Location</a>

        @if(Auth::check())
            <x-profile-dropdown />
        @endif
    </nav>
</header>
