<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user exists by email or Google ID
            $user = User::where('email', $googleUser->getEmail())
                ->orWhere('google_id', $googleUser->getId())
                ->first();

            if ($user) {
                // Update Google ID and email verification if not set
                $updateData = [];
                if (!$user->google_id) {
                    $updateData['google_id'] = $googleUser->getId();
                }
                if (!$user->email_verified_at) {
                    $updateData['email_verified_at'] = now();
                }
                if (!empty($updateData)) {
                    $user->update($updateData);
                }

                // Log the user in
                Auth::login($user, true);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(), // Google emails are verified
                    'password' => Hash::make(uniqid()), // Random password since they use Google
                    'role' => 'member', // Default role
                ]);

                Auth::login($user, true);
            }

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Unable to login with Google. Please try again.');
        }
    }
}

