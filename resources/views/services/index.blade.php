@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-professional">
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
                    <a href="#" class="nav-link">Booking History</a>
                    <a href="{{ route('services.index') }}" class="nav-link active">Services</a>
                    <a href="{{ route('about') }}" class="nav-link">About</a>
                    <a href="{{ route('location') }}" class="nav-link">Location</a>
                    <x-profile-dropdown />
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Header Section -->
        <div class="text-center mb-20">
            <h1 class="hero-title">
                <span class="text-gradient">PREMIUM</span> SERVICES
            </h1>
            <p class="hero-subtitle">
                Experience the pinnacle of workspace excellence with our comprehensive suite of professional services designed to elevate your productivity and success.
            </p>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <!-- Food Service -->
            <div class="service-card group animate-fade-in-up">
                <div class="relative h-72">
                    <img 
                        src="{{ asset('images/blog1.jpg') }}" 
                        alt="Premium Catering Services" 
                        class="service-image"
                    >
                    <div class="service-overlay"></div>
                    <div class="absolute bottom-8 left-8">
                        <h3 class="service-title">
                            <span class="text-yellow-400">S</span>ERVES FOOD
                        </h3>
                        <p class="service-subtitle">Gourmet meals & refreshments</p>
                    </div>
                </div>
                <div class="service-content">
                    <h4>Food Services</h4>
                    <p>
                    We serve delicious meals, fresh coffee, and healthy snacks to keep you full and energized all day.                    </p>
                    <div class="service-footer">

                    </div>
                </div>
            </div>

            <!-- Borrow Service -->
            <div class="service-card group animate-fade-in-up">
                <div class="relative h-72">
                    <img 
                        src="{{ asset('images/blog2.jpg') }}" 
                        alt="Professional Equipment Rental" 
                        class="service-image"
                    >
                    <div class="service-overlay"></div>
                    <div class="absolute bottom-8 left-8">
                        <h3 class="service-title">
                            <span class="text-yellow-400">F</span>AST & RELIABLE WIFI
                        </h3>
                        <p class="service-subtitle">High-Speed Internet Access</p>
                    </div>
                </div>
                <div class="service-content">
                    <h4>Stay Connected Anytime, Anywhere</h4>
                    <p>
                    Experience lightning-fast internet with 24/7 WiFi access. Whether you’re working, streaming, or video conferencing, our high-speed connection keeps you connected without interruptions, so you can stay productive anytime, anywhere.                    </p>
                    <div class="service-footer">
                        </button>
                    </div>
                </div>
            </div>

            <!-- Workspace Service -->
            <div class="service-card group animate-fade-in-up">
                <div class="relative h-72">
                    <img 
                        src="{{ asset('images/Coworking.jpeg') }}" 
                        alt="Executive Workspace Solutions" 
                        class="service-image"
                    >
                    <div class="service-overlay"></div>
                    <div class="absolute bottom-8 left-8">
                        <h3 class="service-title">
                            <span class="text-yellow-400">E</span>PRIVATE WORKSPACES
                        </h3>
                        <p class="service-subtitle">office environments</p>
                    </div>
                </div>
                <div class="service-content">
                    <h4>Premium Workspace Solutions</h4>
                    <p>
                    Enjoy the comfort of your own dedicated space designed for focus and productivity. Our private work areas offer premium amenities, high-speed WiFi, and a professional environment tailored to help you succeed without distractions                    </p>
                    <div class="service-footer">
                    </div>
                </div>
            </div>

            <!-- Meeting Rooms -->
            <div class="service-card group animate-fade-in-up">
                <div class="relative h-72">
                    <img 
                        src="{{ asset('images/blog3.jpg') }}" 
                        alt="Executive Meeting Facilities" 
                        class="service-image"
                    >
                    <div class="service-overlay"></div>
                    <div class="absolute bottom-8 left-8">
                        <h3 class="service-title">
                            <span class="text-yellow-400">C</span>ONFERENCE FACILITIES
                        </h3>
                        <p class="service-subtitle">Professional meeting spaces</p>
                    </div>
                </div>
                <div class="service-content">
                    <h4>Executive Conference Rooms</h4>
                    <p>
                        Host impactful meetings in our state-of-the-art conference facilities equipped with advanced presentation technology, video conferencing capabilities, and professional support services. Make every meeting count with our premium meeting solutions.
                    </p>
                    <div class="service-footer">
                    </div>
                </div>
            </div>

            <!-- Printing Services -->
            <div class="service-card group animate-fade-in-up">
                <div class="relative h-72">
                    <img 
                        src="{{ asset('images/worker1.jpg') }}" 
                        alt="Professional Print Services" 
                        class="service-image"
                    >
                    <div class="service-overlay"></div>
                    <div class="absolute bottom-8 left-8">
                        <h3 class="service-title">
                            <span class="text-yellow-400">P</span>ROFESSIONAL PRINTING
                        </h3>
                        <p class="service-subtitle">High-quality print solutions</p>
                    </div>
                </div>
                <div class="service-content">
                    <h4>Premium Print & Copy Services</h4>
                    <p>
                        Deliver professional results with our comprehensive printing and copying services. From high-resolution color printing to large-format displays, our advanced equipment and expert staff ensure your materials make the right impression every time.
                    </p>
                    <div class="service-footer">
                    </div>
                </div>
            </div>

            <!-- Support Services -->
            <div class="service-card group animate-fade-in-up">
                <div class="relative h-72">
                    <img 
                        src="{{ asset('images/worker2.jpg') }}" 
                        alt="24/7 Professional Support" 
                        class="service-image"
                    >
                    <div class="service-overlay"></div>
                    <div class="absolute bottom-8 left-8">
                        <h3 class="service-title">
                            <span class="text-yellow-400">P</span>ROFESSIONAL SUPPORT
                        </h3>
                        <p class="service-subtitle">24/7 expert assistance</p>
                    </div>
                </div>
                <div class="service-content">
                    <h4>Concierge Support Services</h4>
                    <p>
                        Experience unparalleled support with our dedicated concierge service available 24/7. Our professional team is committed to ensuring your workspace experience is seamless, efficient, and exceeds your expectations at every interaction.
                    </p>
                    <div class="service-footer">
                    </div>
                </div>
            </div>
        </div>
@endsection 