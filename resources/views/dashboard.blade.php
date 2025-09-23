@extends('layouts.app')

@section('content')
<?php $showAvatar = request()->query('show_avatar'); ?>
<div style="background:#222; min-height:100vh; padding:0; margin:0; display:flex; flex-direction:column; align-items:center;">
    <!-- Top Navigation Bar -->
    <div style="width:100vw; background:#fff; display:flex; align-items:center; justify-content:space-between; padding:0 2.5rem; height:64px; box-shadow:0 2px 8px #0001; position:sticky; top:0; z-index:10;">
        <div style="display:flex; align-items:center; gap:2rem;">
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" style="background:#19c2b8; color:#fff; padding:0.5rem 1rem; border-radius:6px; text-decoration:none; font-weight:600; font-size:0.9rem; transition:background-color 0.2s;" onmouseover="this.style.background='#17a8a0'" onmouseout="this.style.background='#19c2b8'">
                Admin Panel
            </a>
            @endif
            <div style="font-size:1.5rem; font-weight:900; color:#19c2b8; letter-spacing:2px;">PWESTO!</div>
        </div>
        <div style="display:flex; align-items:center; gap:2rem;">
            <a href="#" style="font-weight:700; color:#111; text-decoration:none; border-bottom:2px solid #111; padding-bottom:2px;">Home</a>
            <a href="{{ route('booking-history') }}" style="color:#222; text-decoration:none;">Booking History</a>
            <a href="{{ route('services.index') }}" style="color:#222; text-decoration:none;">Services</a>
            <a href="{{ route('about') }}" style="color:#222; text-decoration:none;">About</a>
            <a href="#" style="color:#222; text-decoration:none;">Location</a>
            <a href="{{ route('profile.edit') }}">
                <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/avatar.svg') }}" alt="User" style="width:44px; height:44px; border-radius:50%; object-fit:cover; border:2px solid #eee; {{ !Auth::user()->profile_image ? 'background:#f3f4f6; padding:8px;' : '' }}">
            </a>
        </div>
    </div>
    @if($showAvatar)
    <div style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.7); display:flex; align-items:center; justify-content:center; z-index:1000;">
        <div style="background:#fff; border-radius:24px; padding:2.5rem 2.5rem 2rem 2.5rem; box-shadow:0 8px 32px #0008; display:flex; flex-direction:column; align-items:center; min-width:340px;">
            <img src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/avatar.svg') }}" alt="Profile" style="width:180px; height:180px; border-radius:50%; object-fit:cover; border:4px solid #19c2b8; margin-bottom:2rem; {{ !Auth::user()->profile_image ? 'background:#f3f4f6; padding:2rem;' : '' }}">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" style="margin-bottom:1.5rem; width:100%; text-align:center;">
                @csrf
                @method('PATCH')
                <label for="profile_image" style="font-weight:500;">Upload new profile image:</label><br>
                <input type="file" name="profile_image" id="profile_image" accept="image/*" style="margin:1rem 0;">
                <br>
                <button type="submit" style="background:#19c2b8; color:#fff; border:none; border-radius:8px; padding:0.7rem 2rem; font-size:1rem; font-weight:600; cursor:pointer;">Upload</button>
            </form>
            <a href="{{ route('dashboard') }}" style="color:#19c2b8; font-weight:600; text-decoration:none;">Close</a>
        </div>
    </div>
    @endif
    <div style="width:90vw; max-width:1400px; margin-top:40px; background:#222; border-radius:16px; overflow:hidden; display:flex; flex-direction:column; align-items:center;">
        <div style="position:relative; width:100%; height:520px; background:#222; display:flex; align-items:center; justify-content:center;">
            <img src="{{ asset('images/collab.jpg') }}" alt="Workspace" style="width:100%; height:100%; object-fit:cover;">
            <div style="position:absolute; top:0; left:0; width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <div style="color:#fff; font-size:3.5rem; font-weight:900; text-align:center; margin-bottom:2.5rem; text-shadow:0 2px 8px #000;">
                    Welcome to Pwesto<br>Choose to Book
                </div>
                <div style="display:flex; gap:4rem; justify-content:center; align-items:center;">
                    <a href="{{ route('services.booking') }}" style="display:inline-block; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <img src="{{ asset('images/produktiv.png') }}" alt="Produktivo" style="height:240px; width:240px; border-radius:32px; background:#111; padding:0.5rem; object-fit:contain; box-shadow:0 4px 24px #0006;">
                    </a>
                    <a href="{{ route('services.nest-booking') }}" style="display:inline-block; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <img src="{{ asset('images/nest.png') }}" alt="Nest" style="height:240px; width:240px; border-radius:32px; background:#111; padding:0.5rem; object-fit:contain; box-shadow:0 4px 24px #0006;">
                    </a>
                    <a href="#" style="display:inline-block; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <img src="{{ asset('images/media.jpg') }}" alt="Mesh Media" style="height:240px; width:240px; border-radius:32px; background:#111; padding:0.5rem; object-fit:contain; box-shadow:0 4px 24px #0006;">
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 