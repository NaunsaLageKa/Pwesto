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
                    <a href="#" class="nav-link">About</a>
                    <a href="#" class="nav-link">Location</a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center">
                        <img 
                            src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/avatar.svg') }}" 
                            alt="Profile" 
                            class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 {{ !Auth::user()->profile_image ? 'bg-gray-100 p-2' : '' }}"
                        >
                    </a>
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
                            <span class="text-yellow-400">P</span>REMIUM CATERING
                        </h3>
                        <p class="service-subtitle">Gourmet meals & refreshments</p>
                    </div>
                </div>
                <div class="service-content">
                    <h4>Executive Catering Services</h4>
                    <p>
                        Indulge in our premium catering services featuring gourmet meals, artisanal coffee, and healthy refreshments. Our professional culinary team ensures every meal is crafted with excellence to keep you energized and focused throughout your workday.
                    </p>
                    <div class="service-footer">
                        <span class="service-status">Premium Available</span>
                        <button class="service-button">
                            Explore Services
                        </button>
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
                            <span class="text-yellow-400">E</span>QUIPMENT RENTAL
                        </h3>
                        <p class="service-subtitle">Professional tools & technology</p>
                    </div>
                </div>
                <div class="service-content">
                    <h4>Professional Equipment Solutions</h4>
                    <p>
                        Access cutting-edge technology and professional equipment when you need it most. From high-performance laptops and presentation systems to specialized business tools, we provide everything you need to succeed in today's competitive environment.
                    </p>
                    <div class="service-footer">
                        <span class="service-status">Premium Available</span>
                        <button class="service-button">
                            Explore Services
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
                            <span class="text-yellow-400">E</span>XECUTIVE WORKSPACES
                        </h3>
                        <p class="service-subtitle">Premium office environments</p>
                    </div>
                </div>
                <div class="service-content">
                    <h4>Premium Workspace Solutions</h4>
                    <p>
                        Experience the ultimate in workspace luxury with our executive offices, private suites, and collaborative environments. Each space is meticulously designed with premium amenities, high-speed connectivity, and professional services to support your success.
                    </p>
                    <div class="service-footer">
                        <span class="service-status">Premium Available</span>
                        <button class="service-button">
                            Explore Services
                        </button>
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
                        <span class="service-status">Premium Available</span>
                        <button class="service-button">
                            Explore Services
                        </button>
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
                        <span class="service-status">Premium Available</span>
                        <button class="service-button">
                            Explore Services
                        </button>
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
                        <span class="service-status">Premium Available</span>
                        <button class="service-button">
                            Explore Services
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-20">
            <div class="cta-section">
                <h2 class="cta-title">Ready to Experience Excellence?</h2>
                <p class="cta-subtitle">
                    Join the elite professionals who choose PWESTO for their workspace needs. 
                    Discover how our premium services can transform your productivity and elevate your success.
                </p>
                <a href="{{ route('dashboard') }}" class="cta-button">
                    Reserve Your Premium Workspace
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 