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
                    <a href="#" class="nav-link">About</a>
                    <a href="#" class="nav-link">Location</a>
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <img 
                            src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/avatar.svg') }}" 
                            alt="Profile" 
                            class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 {{ !Auth::user()->profile_image ? 'bg-gray-100 p-2' : '' }}"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Header Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-white mb-4">
                Book a space
            </h1>
            <p class="text-xl text-gray-300">
                Find the perfect workspace for your needs.
            </p>
        </div>

        <!-- Available Spaces Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-white mb-8">Available Spaces</h2>
            
            <!-- Grid layout: Each row has text and image side by side -->
            <div class="space-y-8">
                <!-- Row 1: Hot Desk 1 -->
                <div class="flex gap-12 items-start">
                    <!-- Left - Text Information -->
                    <div class="flex-1 text-white">
                        <h3 class="text-2xl font-bold text-yellow-500 mb-3">Hot Desk</h3>
                        <p class="text-white text-lg mb-6">
                            Flexible desk space in a shared area. Perfect for individuals.
                        </p>
                        <div class="flex space-x-4">
                            <button class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                                View Details
                            </button>
                            <a href="{{ route('services.select-seat', ['service' => 'hot-desk']) }}" class="px-6 py-3 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors inline-block">
                                BOOK
                            </a>
                        </div>
                    </div>

                                         <!-- Right - Image -->
                     <div class="w-96">
                         <div class="h-56 rounded-lg overflow-hidden bg-black flex items-center justify-center p-1">
                             <img src="{{ asset('images/produktiv.png') }}" alt="Hot Desk Space" class="max-w-full max-h-full object-contain" style="max-height: 200px;">
                         </div>
                     </div>
                </div>

                <!-- Row 2: Take a Nap -->
                <div class="flex gap-12 items-start">
                    <!-- Left - Text Information -->
                    <div class="flex-1 text-white">
                        <h3 class="text-2xl font-bold text-yellow-500 mb-3">Take a Nap</h3>
                        <p class="text-white text-lg mb-6">
                            Flexible Napping Room where you can rest.
                        </p>
                        <div class="flex space-x-4">
                            <button class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                                View Details
                            </button>
                            <a href="{{ route('services.select-seat', ['service' => 'napping-room']) }}" class="px-6 py-3 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors inline-block">
                                BOOK
                            </a>
                        </div>
                    </div>

                                         <!-- Right - Image -->
                     <div class="w-96">
                         <div class="h-56 rounded-lg overflow-hidden bg-black flex items-center justify-center p-1">
                             <img src="{{ asset('images/nest.png') }}" alt="Napping Room" class="max-w-full max-h-full object-contain" style="max-height: 200px;">
                         </div>
                     </div>
                </div>

                <!-- Row 3: Hot Desk 2 -->
                <div class="flex gap-12 items-start">
                    <!-- Left - Text Information -->
                    <div class="flex-1 text-white">
                        <h3 class="text-2xl font-bold text-yellow-500 mb-3">Hot Desk</h3>
                        <p class="text-white text-lg mb-6">
                            Flexible desk space in a shared area. Perfect for individuals.
                        </p>
                        <div class="flex space-x-4">
                            <button class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                                View Details
                            </button>
                            <a href="{{ route('services.select-seat', ['service' => 'hot-desk']) }}" class="px-6 py-3 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors inline-block">
                                BOOK
                            </a>
                        </div>
                    </div>

                                         <!-- Right - Image -->
                     <div class="w-96">
                         <div class="h-56 rounded-lg overflow-hidden bg-black flex items-center justify-center p-1">
                             <img src="{{ asset('images/media.jpg') }}" alt="Hot Desk Space" class="max-w-full max-h-full object-contain" style="max-height: 200px;">
                         </div>
                     </div>
                </div>
            </div>
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
