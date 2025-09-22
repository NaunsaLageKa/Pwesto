@extends('layouts.app')

@section('content')
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f8fbfd;">
    <div style="width:100%;max-width:400px;margin:auto;">
        <div style="text-align:center;margin-bottom:2rem;">
            <div style="font-size:2.5rem;font-weight:bold;color:#19c2b8;letter-spacing:2px;">PWESTO!</div>
            <div style="color:#222;font-size:1rem;margin-top:0.25rem;">Reserve Your Space, Work Your Way</div>
        </div>
        <?php if (
isset($errors) && $errors->any()): ?>
            <div style="color: red; margin-bottom: 1rem;">
                <ul>
                    <?php foreach ($errors->all() as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST" action="{{ route('login') }}" style="background:#fff;padding:2rem;border-radius:10px;box-shadow:0 2px 8px #0001;">
            @csrf
            <div style="margin-bottom:1rem;text-align:left;">
                <label for="email" style="font-weight:500;">Email:</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your Email"
                    style="width:100%;padding:0.75rem;border:1px solid #e0e0e0;border-radius:6px;margin-top:0.25rem;background:#f3f6f9;">
            </div>
            <div style="margin-bottom:1.5rem;text-align:left;">
                <label for="password" style="font-weight:500;">Password</label>
                <input id="password" type="password" name="password" required placeholder="Enter Password"
                    style="width:100%;padding:0.75rem;border:1px solid #e0e0e0;border-radius:6px;margin-top:0.25rem;background:#f3f6f9;">
            </div>
            <button type="submit" style="width:100%;background:#1976d2;color:#fff;padding:0.75rem;border:none;border-radius:6px;font-size:1.1rem;font-weight:500;cursor:pointer;">
                Sign in
            </button>
        </form>
        <div style="text-align:center;margin-top:1rem;">
            Don't have an account? <a href="{{ route('register') }}" style="color:#1976d2;">Sign up</a>
        </div>
    </div>
</div>
@endsection
