<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationOtp;
use App\Models\Member;
use App\Models\User;
use App\Notifications\EmailVerificationOtpNotification;
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
            'phone' => ['nullable', 'string', 'max:20', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => User::ROLE_MEMBER, // Automatically set to member for all registrations
            'password' => Hash::make($request->password),
        ]);

        // Automatically create Member profile for members
        if ($user->isMember()) {
            Member::create([
                'user_id' => $user->id,
                'full_name' => $user->name,
                'phone' => $user->phone ?? '',
                'email' => $user->email,
                'date_of_conversion' => now()->toDateString(), // Use registration date as conversion date
                'preferred_contact' => 'email', // Default to email (SMS removed)
            ]);
        }

        event(new Registered($user));

        // Auto-verify email for immediate access
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));

        Auth::login($user);

        // Redirect directly to dashboard
        return redirect()->route('dashboard')->with('status', 'registration-successful');
    }
}
