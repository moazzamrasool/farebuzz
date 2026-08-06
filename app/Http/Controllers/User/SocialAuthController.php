<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // Google & Facebook are handled via Socialite (built-in)
    // Apple requires socialiteproviders/apple package — currently shows "coming soon"
    private array $providers = ['google', 'facebook'];

    public function redirect(string $provider)
    {
        if ($provider === 'apple') {
            return redirect()->route('user.login')
                ->with('error', 'Apple Sign-In is coming soon. Please use Google, Facebook, or email to login.');
        }

        abort_unless(in_array($provider, $this->providers), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);

        try {
            $social = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('user.login')
                ->with('error', 'Social login failed. Please try again or use email & password.');
        }

        $user = User::firstOrCreate(
            ['email' => $social->getEmail()],
            [
                'name'     => $social->getName() ?? $social->getNickname() ?? 'User',
                'password' => bcrypt(Str::random(24)),
                'role'     => 'customer',
            ]
        );

        Auth::login($user, remember: true);

        return redirect()->intended(route('user.dashboard'));
    }
}
