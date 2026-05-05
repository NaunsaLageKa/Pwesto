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
                    @if(Auth::user()->role === 'hub_owner')
                    <a href="{{ route('hub-owner.dashboard') }}" class="nav-link">Dashboard</a>
                    <a href="{{ route('hub-owner.floor-plan') }}" class="nav-link">Floor Plan</a>
                    @endif
                    <a href="{{ route('about') }}" class="nav-link">About</a>
                    <a href="{{ route('location') }}" class="nav-link">Location</a>
                    <x-profile-dropdown />
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen relative px-4 sm:px-6 lg:px-8">
        <div class="text-content">
            <h1 class="mesh-title">
                Mesh Media
            </h1>
            <h2 class="book-title">
                Book a space
            </h2>
            <p class="price-text">
                ₱175/hour
            </p>
            <a href="{{ route('services.select-seat', ['service' => 'meeting-room']) }}" class="book-button mesh-book-btn">
                BOOK
            </a>
        </div>

        <div class="image-container">
            <div class="workspace-image">
                <div class="carousel-container">
                    <div class="carousel-slide mesh-carousel-slide active">
                        <img src="{{ asset('images/media.jpg') }}" alt="Mesh Media workspace" class="w-full h-full object-cover">
                        <div class="description-text">Creative workspace &amp; meeting rooms.</div>
                    </div>
                    <div class="carousel-slide mesh-carousel-slide">
                        <img src="{{ asset('images/media.jpg') }}" alt="Mesh Media collaboration" class="w-full h-full object-cover">
                        <div class="description-text">Collaborate in a modern media-ready space.</div>
                    </div>
                    <div class="carousel-slide mesh-carousel-slide">
                        <img src="{{ asset('images/media.jpg') }}" alt="Mesh Media venue" class="w-full h-full object-cover">
                        <div class="description-text">Flexible bookings through PWESTO.</div>
                    </div>
                </div>
                <div class="carousel-dots mesh-carousel-dots">
                    <span class="dot mesh-dot active" onclick="meshCurrentSlide(1)"></span>
                    <span class="dot mesh-dot" onclick="meshCurrentSlide(2)"></span>
                    <span class="dot mesh-dot" onclick="meshCurrentSlide(3)"></span>
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
    @apply text-teal-600 font-semibold;
}

.admin-button {
    @apply bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors font-medium;
}

.text-content {
    position: absolute;
    left: 120px;
    top: 100px;
    z-index: 10;
    max-width: 500px;
}

.mesh-title {
    font-size: 5rem;
    font-weight: bold;
    color: #4ecdc4;
    margin-bottom: 1.5rem;
    line-height: 0.9;
    letter-spacing: -0.02em;
}

.book-title {
    font-size: 4.5rem;
    font-weight: bold;
    color: white;
    margin-bottom: 1rem;
    line-height: 1;
}

.price-text {
    font-size: 3rem;
    font-weight: 600;
    color: white;
    margin-bottom: 1.5rem;
}

.description-text {
    position: absolute;
    font-size: 2rem;
    font-weight: bold;
    color: white;
    line-height: 1.3;
    max-width: 400px;
    left: 50%;
    transform: translateX(-50%);
    bottom: 72px;
    text-align: center;
    width: 90%;
    z-index: 5;
}

.mesh-book-btn {
    display: inline-block;
    background-color: #2a9d8f;
    color: white;
    padding: 2rem 4rem;
    border-radius: 1rem;
    text-decoration: none;
    font-weight: bold;
    font-size: 2rem;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(42, 157, 143, 0.35);
    border: none;
    cursor: pointer;
}

.mesh-book-btn:hover {
    background-color: #238f82;
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(42, 157, 143, 0.45);
}

.image-container {
    position: absolute;
    right: 50px;
    top: 140px;
    z-index: 5;
    width: 800px;
    height: 600px;
}

.workspace-image {
    width: 100%;
    height: 100%;
    border-radius: 1.2rem;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    position: relative;
}

.carousel-container {
    width: 100%;
    height: 100%;
    position: relative;
}

.mesh-carousel-slide {
    display: none;
    width: 100%;
    height: 100%;
    position: relative;
}

.mesh-carousel-slide.active {
    display: block;
}

.mesh-carousel-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.carousel-dots {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 10;
}

.mesh-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.mesh-dot.active {
    background-color: white;
}

.mesh-dot:hover {
    background-color: rgba(255, 255, 255, 0.85);
}

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

    .mesh-title {
        font-size: 4rem;
    }

    .book-title {
        font-size: 3.25rem;
    }

    .price-text {
        font-size: 2.5rem;
    }

    .mesh-book-btn {
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
let meshSlideIndex = 1;

function meshShowSlides(n) {
    const slides = document.getElementsByClassName('mesh-carousel-slide');
    const dots = document.getElementsByClassName('mesh-dot');

    if (n > slides.length) { meshSlideIndex = 1; }
    if (n < 1) { meshSlideIndex = slides.length; }

    for (let i = 0; i < slides.length; i++) {
        slides[i].classList.remove('active');
    }
    for (let i = 0; i < dots.length; i++) {
        dots[i].classList.remove('active');
    }

    slides[meshSlideIndex - 1].classList.add('active');
    dots[meshSlideIndex - 1].classList.add('active');
}

function meshCurrentSlide(n) {
    meshShowSlides(meshSlideIndex = n);
}

function meshAutoAdvance() {
    meshSlideIndex++;
    meshShowSlides(meshSlideIndex);
}

document.addEventListener('DOMContentLoaded', function() {
    meshShowSlides(meshSlideIndex);
    setInterval(meshAutoAdvance, 3000);
});
</script>
@endsection
