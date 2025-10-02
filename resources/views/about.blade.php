@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-800">
    <!-- Navigation Header -->
    <div class="bg-white shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-6">
                    @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="admin-button">
                        Admin Panel
                    </a>
                    @endif
                    <div class="text-2xl font-bold text-teal-600 tracking-wider">PWESTO!</div>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="{{ route('dashboard') }}" class="nav-link">Home</a>
                    <a href="{{ route('booking-history') }}" class="nav-link">Booking History</a>
                    <a href="{{ route('services.index') }}" class="nav-link">Services</a>
                    <a href="{{ route('about') }}" class="nav-link active">About</a>
                    <a href="{{ route('location') }}" class="nav-link">Location</a>
                    <x-profile-dropdown />
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Header Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-white mb-4">
                About Pwesto
            </h1>
            <p class="text-xl text-gray-300">
                Your modern workspace booking platform
            </p>
        </div>

        <!-- Purpose Section -->
        <div class="bg-gray-700 rounded-xl p-8 mb-12 shadow-lg">
            <div class="text-center mb-8">
                <div class="w-32 h-32 bg-teal-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-white mb-4">Our Purpose</h2>
            </div>
            
            <div class="max-w-4xl mx-auto">
                <p class="text-lg text-gray-300 leading-relaxed mb-6">
                    Pwesto is designed to revolutionize how people book and manage workspace reservations. 
                    We understand that modern work requires flexibility, and our platform provides seamless 
                    access to various workspace options tailored to your needs.
                </p>
                
                <p class="text-lg text-gray-300 leading-relaxed mb-6">
                    Whether you need a hot desk for a few hours, a private office for focused work, 
                    or a collaborative space for team meetings, Pwesto connects you with the perfect 
                    workspace solution.
                </p>
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-gray-700 rounded-xl p-6 text-center shadow-lg">
                <div class="w-24 h-24 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Easy Booking</h3>
                <p class="text-gray-300">
                    Book your workspace in just a few clicks with our intuitive interface
                </p>
            </div>

            <div class="bg-gray-700 rounded-xl p-6 text-center shadow-lg">
                <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Real-time Availability</h3>
                <p class="text-gray-300">
                    See live availability and book instantly with our dynamic floor plan system
                </p>
            </div>

            <div class="bg-gray-700 rounded-xl p-6 text-center shadow-lg">
                <div class="w-24 h-24 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-3">Booking History</h3>
                <p class="text-gray-300">
                    Track all your bookings with detailed history and easy rebooking options
                </p>
            </div>
        </div>
        <div class="bg-gradient-to-r from-teal-600 to-blue-600 rounded-xl p-8 text-center shadow-lg">
            <h2 class="text-3xl font-bold text-white mb-4">Our Mission</h2>
            <p class="text-xl text-white leading-relaxed max-w-4xl mx-auto">
                To provide flexible, accessible, and efficient workspace solutions that empower 
                individuals and teams to work productively in today's dynamic business environment.
            </p>
        </div>
    </div>
</div>

<style>
.nav-link {
    @apply text-gray-700 hover:text-teal-600 font-medium transition-colors;
}

.nav-link.active {
    @apply text-teal-600 border-b-2 border-teal-600 pb-1;
}

.admin-button {
    @apply bg-teal-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-teal-700 transition-colors;
}
</style>
@endsection
