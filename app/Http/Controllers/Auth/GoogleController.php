<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                Auth::login($user);
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'occupation' => 'Google User',
                    'avatar' => $googleUser->avatar ?? 'default-avatar.png',
                ]);

                Auth::login($user);
            }

            return redirect()->intended('/');

        } catch (\Exception $e) {
            // Tampilkan error detail
            return redirect()->route('login')->with('error', 'Failed to login with Google: ' . $e->getMessage());
        }
    }
}
