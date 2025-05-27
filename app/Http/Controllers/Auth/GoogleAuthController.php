<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email', 'https://www.googleapis.com/auth/gmail.readonly'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();
            
            $user = User::updateOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name' => $socialUser->getName(),
                    'google_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]
            );

            Auth::login($user);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            \Log::error('Google auth failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Google authentication failed');
        }
    }
}
