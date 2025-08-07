<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:255'],
        ]);

        $role = $request->input('role', 'user');
        $status = ($role === 'hub_owner') ? 'pending' : 'approved';
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $role,
            'status' => $status,
        ];
        if ($role === 'hub_owner') {
            $data['company'] = $request->company;
            if ($request->hasFile('company_id')) {
                $data['company_id'] = $request->file('company_id')->store('company_ids', 'public');
            }
        }
        $user = User::create($data);

        event(new Registered($user));

        // Auth::login($user); // Remove auto-login after registration

        // Redirect to login page after registration
        return redirect()->route('login')->with('status', 'Registration successful! Please log in.');
    }
}
