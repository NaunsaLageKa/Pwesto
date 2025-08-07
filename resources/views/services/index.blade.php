@extends('layouts.app')

@section('content')
<div style="background:#222; min-height:100vh; display:flex; flex-direction:column; align-items:center;">
    <div style="width:100vw; background:#fff; display:flex; align-items:center; justify-content:space-between; padding:0 2.5rem; height:64px; box-shadow:0 2px 8px #0001; position:sticky; top:0; z-index:10;">
        <div style="font-size:1.5rem; font-weight:900; color:#19c2b8; letter-spacing:2px;">PWESTO!</div>
    </div>
    <div style="width:90vw; max-width:1000px; margin-top:40px; background:#222; border-radius:16px; overflow:hidden; display:flex; flex-direction:column; align-items:center;">
        <div style="margin: 2rem 0;">
            <span style="font-size:3rem; font-weight:900; color:#fff; letter-spacing:2px;">
                <span style="color:#ffe14d;">OFFER</span> SERVICES
            </span>
        </div>
        <div style="width:90%; margin-bottom:2rem;">
            <div style="position:relative; margin-bottom:2rem;">
                <img src='{{ asset('images/blog 1.jpg') }}' alt='Foods' style='width:100%; height:90px; object-fit:cover; border-radius:12px;'>
                <span style='position:absolute; left:30px; top:50%; transform:translateY(-50%); font-size:2.5rem; color:#fff; font-weight:600; letter-spacing:2px; text-shadow:0 2px 8px #000;'>
                    <span style="color:#ffe14d;">F</span>OODS
                </span>
            </div>
            <div style="position:relative; margin-bottom:2rem;">
                <img src='{{ asset('images/blog2.jpg') }}' alt='Borrow' style='width:100%; height:90px; object-fit:cover; border-radius:12px;'>
                <span style='position:absolute; left:30px; top:50%; transform:translateY(-50%); font-size:2.5rem; color:#fff; font-weight:600; letter-spacing:2px; text-shadow:0 2px 8px #000;'>
                    <span style="color:#ffe14d;">B</span>orrow
                </span>
            </div>
        </div>
    </div>
</div>
@endsection 