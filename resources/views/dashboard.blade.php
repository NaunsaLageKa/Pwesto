@extends('layouts.app')

@section('content')
<?php $showAvatar = request()->query('show_avatar'); ?>
<div class="dashboard-page">
    @include('partials.dashboard-navbar', ['active' => 'home'])

    @if($showAvatar)
        <div class="avatar-modal">
            <div class="avatar-modal-card">
                <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/avatar.svg') }}" alt="Profile" class="avatar-modal-img {{ !Auth::user()->profile_image ? 'avatar-fallback-large' : '' }}">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="avatar-modal-form">
                @csrf
                @method('PATCH')
                    <label for="profile_image" class="avatar-modal-label">Upload new profile image:</label>
                    <input type="file" name="profile_image" id="profile_image" accept="image/*" class="avatar-modal-input">
                    <button type="submit" class="avatar-modal-btn">Upload</button>
            </form>
                <a href="{{ route('dashboard') }}" class="avatar-modal-close">Close</a>
            </div>
        </div>
    @endif

    <div class="dashboard-hero-wrapper">
        <div class="dashboard-hero">
            <img src="{{ asset('images/collab.jpg') }}" alt="Workspace" class="dashboard-hero-bg">
            <div class="dashboard-hero-overlay">
                <div class="dashboard-hero-title">
                    Welcome to Pwesto<br>Choose to Book
                </div>
                <div class="dashboard-company-grid">
                    <a href="{{ route('services.booking') }}" class="company-card-link">
                        <img src="{{ asset('images/produktiv.png') }}" alt="Produktivo" class="company-card-logo">
                    </a>
                    <a href="{{ route('services.nest-booking') }}" class="company-card-link">
                        <img src="{{ asset('images/nest.png') }}" alt="Nest" class="company-card-logo">
                    </a>
                    <a href="{{ route('services.mesh-booking') }}" class="company-card-link">
                        <img src="{{ asset('images/media.jpg') }}" alt="Mesh Media" class="company-card-logo">
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section class="dashboard-about bg-gray-800" aria-labelledby="about-pwesto-main-heading">
        @include('partials.about-content')
    </section>
</div>

<style>
.dashboard-page {
    background: #ffffff;
    min-height: 100vh;
    overflow-x: hidden;
    overflow-y: auto;
}
.dashboard-hero-wrapper {
    width: 100%;
    margin: 0;
    overflow: hidden;
    min-height: calc(100vh - 72px);
}
.dashboard-hero {
    position: relative;
    min-height: calc(100vh - 72px);
}
.dashboard-hero-bg {
    width: 100%;
    height: 100%;
    min-height: calc(100vh - 72px);
    object-fit: cover;
}
.dashboard-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(17, 24, 39, 0.35), rgba(17, 24, 39, 0.45));
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}
.dashboard-hero-title {
    color: #fff;
    font-size: clamp(2.4rem, 5vw, 4.3rem);
    font-weight: 900;
    text-align: center;
    line-height: 1.15;
    margin-bottom: 2rem;
    text-shadow: 0 4px 16px rgba(0, 0, 0, 0.55);
}
.dashboard-company-grid {
    display: flex;
    gap: clamp(1rem, 3vw, 3.2rem);
    flex-wrap: wrap;
    justify-content: center;
}
.company-card-link {
    display: inline-flex;
    border-radius: 24px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.company-card-link:hover {
    transform: translateY(-4px) scale(1.02);
}
.company-card-logo {
    width: clamp(175px, 19vw, 260px);
    height: clamp(175px, 19vw, 260px);
    border-radius: 24px;
    background: #050505;
    object-fit: contain;
    padding: 0.5rem;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.38);
}
.avatar-modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.72);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
}
.avatar-modal-card {
    background: #fff;
    border-radius: 18px;
    padding: 2rem;
    min-width: 340px;
    max-width: 90vw;
    text-align: center;
}
.avatar-modal-img {
    width: 170px;
    height: 170px;
    border-radius: 9999px;
    object-fit: cover;
    border: 4px solid #14b8a6;
    margin: 0 auto 1.25rem;
}
.avatar-fallback-large {
    background: #f3f4f6;
    padding: 1.5rem;
}
.avatar-modal-form {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.avatar-modal-label {
    font-weight: 600;
}
.avatar-modal-input {
    font-size: 0.92rem;
}
.avatar-modal-btn {
    background: #14b8a6;
    color: #fff;
    border: 0;
    border-radius: 8px;
    padding: 0.65rem 1rem;
    font-weight: 700;
    cursor: pointer;
}
.avatar-modal-close {
    display: inline-block;
    margin-top: 1rem;
    color: #0f766e;
    text-decoration: none;
    font-weight: 700;
}

.dashboard-about {
    border-top: 1px solid rgba(55, 65, 81, 0.6);
}

@media (max-width: 1024px) {
    .dashboard-hero-wrapper,
    .dashboard-hero {
        min-height: calc(100vh - 72px);
    }
    .dashboard-hero-bg {
        min-height: calc(100vh - 72px);
    }
}
</style>
@endsection 