<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect based on role
        $user = Auth::user();
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($user && $user->role === 'hub_owner') {
            if ($user->status === 'approved') {
                return redirect()->route('hub-owner.dashboard');
            } elseif ($user->status === 'pending') {
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Your registration is pending admin approval.']);
            } elseif ($user->status === 'rejected') {
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Your registration was rejected.']);
            }
        }
        // Prevent banned users from logging in
        if ($user && $user->status === 'banned') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account has been banned.']);
        }
        // Redirect to the main dashboard after login
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
