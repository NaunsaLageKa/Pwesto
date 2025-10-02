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
                    <a href="{{ route('about') }}" class="nav-link">About</a>
                    <a href="{{ route('location') }}" class="nav-link">Location</a>
                    <x-profile-dropdown />
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen relative px-4 sm:px-6 lg:px-8">
        <!-- Left Content - Manually Positioned -->
        <div class="text-content">
            <h1 class="produktiv-title">
                Produktiv
            </h1>
            <h2 class="book-title">
                Book a space
            </h2>
            <p class="price-text">
                ₱150/hour
            </p>
            <a href="{{ route('services.select-seat', ['service' => 'hot-desk']) }}" class="book-button">
                BOOK
            </a>
        </div>

        <!-- Right Content - Large Image - Manually Positioned -->
        <div class="image-container">
            <div class="workspace-image">
                <div class="carousel-container">
                    <div class="carousel-slide active">
                        <img src="{{ asset('images/produktiv123.jpg') }}" alt="Perfect Spot For napping" class="w-full h-full object-cover">
                        <div class="description-text">Perfect Spot For napping.</div>
                    </div>
                    <div class="carousel-slide">
                        <img src="{{ asset('images/produktiv1234.jpg') }}" alt="Find the perfect workspace for your needs" class="w-full h-full object-cover">
                        <div class="description-text">Find the perfect workspace for your needs.</div>
                    </div>
                    <div class="carousel-slide">
                        <img src="{{ asset('images/produktivenapping.jpg') }}" alt="A perfect Spot for Friends and individuals" class="w-full h-full object-cover">
                        <div class="description-text">A perfect Spot for Friends and individuals</div>
                    </div>
                </div>
                <!-- Carousel Navigation Dots -->
                <div class="carousel-dots">
                    <span class="dot active" onclick="currentSlide(1)"></span>
                    <span class="dot" onclick="currentSlide(2)"></span>
                    <span class="dot" onclick="currentSlide(3)"></span>
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

/* Manual Positioning Controls - Match the Image Layout */
.text-content {
    position: absolute;
    left: 120px;        /* Move text more to the right */
    top: 100px;        /* Move text down a bit */
    z-index: 10;
    max-width: 500px;
}

.produktiv-title {
    font-size: 5rem;   /* Slightly smaller to match image */
    font-weight: bold;
    color: #fbbf24;    /* Bright yellow */
    margin-bottom: 1.5rem;
    line-height: 0.9;
    letter-spacing: -0.02em;
}

.book-title {
    font-size: 4.5rem; /* Smaller subtitle */
    font-weight: bold;
    color: white;
    margin-bottom: 1rem;
    line-height: 1;
    
}

.price-text {
    font-size: 3rem;   /* Smaller price */
    font-weight: 600;
    color: white;
    margin-bottom: 1.5rem;
}

.description-text {
    position: absolute;
    font-size: 3rem;   /* Smaller description */
    font-weight: bold; /* Make text bold */
    color: white;      /* White instead of gray */
    line-height: 1.3;
    max-width: 400px;
    left: 230px;   
    top: 400px;     /* Below the image (600px height + 20px spacing) */
    text-align: center;
    width: 100%;       /* Full width of image container */
}

/* Description text inside carousel slides */
.carousel-slide .description-text {
    position: absolute;
    font-size: 3rem;
    font-weight: bold;
    color: white;
    line-height: 1.3;
    max-width: 400px;
    left: 230px;   
    top: 400px;
    text-align: center;
    width: 100%;
    z-index: 5;
}

.book-button {
    display: inline-block;
    background-color: #4a5d23;  /* Darker green like in image */
    color: white;
    font-weight: bold;
    padding: 1.2rem 3rem;       /* Smaller button */
    border-radius: 0.5rem;
    font-size: 1.5rem;          /* Smaller button text */
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.book-button:hover {
    background-color: #5a6d33;
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
}

/* Image Positioning - Match the Image Layout */
.image-container {
    position: absolute;
    right: 50px;       /* Move image more to the left */
    top: 140px;        /* Move image up a bit */
    z-index: 5;
    width: 800px;      /* Smaller image */
    height: 600px;     /* Square aspect ratio */
}

.workspace-image {
    width: 100%;
    height: 100%;
    border-radius: 1.2rem;  /* Slightly less rounded */
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    position: relative;
}

/* Carousel Styles */
.carousel-container {
    width: 100%;
    height: 100%;
    position: relative;
}

.carousel-slide {
    display: none;
    width: 100%;
    height: 100%;
    position: relative;
}

.carousel-slide.active {
    display: block;
}

.carousel-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Carousel Navigation Dots */
.carousel-dots {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 10;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.dot.active {
    background-color: white;
}

.dot:hover {
    background-color: rgba(255, 255, 255, 0.8);
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .text-content {
        left: 20px;
        top: 100px;
        max-width: 90%;
    }
    
    .image-container {
        right: 20px;
        top: 80px;
        width: 400px;
        height: 400px;
    }
    
    .produktiv-title {
        font-size: 6rem;    
    }
    
    .book-title {
        font-size: 4rem;
    }
    
    .price-text {
        font-size: 3rem;
    }
    
    .description-text {
        font-size: 2rem;
    }
    
    .book-button {
        font-size: 1.5rem;
        padding: 1rem 3rem;
    }
}

@media (max-width: 768px) {
    .text-content {
        position: relative;
        left: auto;
        top: auto;
        text-align: center;
        padding: 2rem;
    }
    
    .image-container {
        position: relative;
        right: auto;
        top: auto;
        margin: 2rem auto;
        width: 300px;
        height: 300px;
    }
}
</style>

<script>
let slideIndex = 1;

function showSlides(n) {
    let slides = document.getElementsByClassName("carousel-slide");
    let dots = document.getElementsByClassName("dot");
    
    if (n > slides.length) {slideIndex = 1}
    if (n < 1) {slideIndex = slides.length}
    
    for (let i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active");
    }
    
    for (let i = 0; i < dots.length; i++) {
        dots[i].classList.remove("active");
    }
    
    slides[slideIndex-1].classList.add("active");
    dots[slideIndex-1].classList.add("active");
}

function currentSlide(n) {
    showSlides(slideIndex = n);
}

// Auto-advance slides every 5 seconds
function autoAdvance() {
    slideIndex++;
    showSlides(slideIndex);
}

// Initialize carousel
document.addEventListener('DOMContentLoaded', function() {
    showSlides(slideIndex);
    setInterval(autoAdvance, 3000); // Auto-advance every 5 seconds
});
</script>
@endsection
