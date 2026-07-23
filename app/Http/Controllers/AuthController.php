<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\RequestException;
use App\Models\User;

use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $ip = $request->ip();
        $lockoutUntilKey = 'login_lockout_until_' . $ip;

        if (Cache::has($lockoutUntilKey)) {
            $lockoutUntil = Cache::get($lockoutUntilKey);
            $seconds = $lockoutUntil - time();
            if ($seconds > 0) {
                $errors = new \Illuminate\Support\MessageBag([
                    'username' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.'
                ]);
                return view('auth.login')->with('errors', $errors);
            } else {
                Cache::forget($lockoutUntilKey);
            }
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username is required',
            'password.required' => 'Password is required',
        ]);

        $ip = $request->ip();
        $attemptsKey = 'login_attempts_' . $ip;
        $lockoutUntilKey = 'login_lockout_until_' . $ip;

        // Check if currently locked out
        if (Cache::has($lockoutUntilKey)) {
            $lockoutUntil = Cache::get($lockoutUntilKey);
            $seconds = $lockoutUntil - time();
            if ($seconds > 0) {
                return back()->withErrors([
                    'username' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.',
                ])->withInput($request->only('username'));
            } else {
                Cache::forget($lockoutUntilKey);
            }
        }

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            $attempts = Cache::get($attemptsKey, 0) + 1;
            Cache::put($attemptsKey, $attempts, 3600); // Simpan jumlah percobaan selama 1 jam

            if ($attempts >= 3) {
                $lockSeconds = ($attempts == 3) ? 15 : (($attempts == 4) ? 30 : 60);
                Cache::put($lockoutUntilKey, time() + $lockSeconds, $lockSeconds);
                return back()->withErrors([
                    'username' => 'Too many login attempts. Please try again in ' . $lockSeconds . ' seconds.',
                ])->withInput($request->only('username'));
            }

            return back()->withErrors([
                'username' => 'Username not found',
            ])->withInput($request->only('username'));
        }

        $passwordMatch = ($user->password === $request->password) || Hash::check($request->password, $user->password);
        $epicorPasswordMatch = ($user->epicor_password === $request->password) || Hash::check($request->password, $user->epicor_password);

        if (!$passwordMatch && !$epicorPasswordMatch) {
            $attempts = Cache::get($attemptsKey, 0) + 1;
            Cache::put($attemptsKey, $attempts, 3600);

            if ($attempts >= 3) {
                $lockSeconds = ($attempts == 3) ? 15 : (($attempts == 4) ? 30 : 60);
                Cache::put($lockoutUntilKey, time() + $lockSeconds, $lockSeconds);
                return back()->withErrors([
                    'username' => 'Too many login attempts. Please try again in ' . $lockSeconds . ' seconds.',
                ])->withInput($request->only('username'));
            }

            return back()->withErrors([
                'password' => 'Password incorrect',
            ])->withInput($request->only('username'));
        }

        // Login sukses, hapus data percobaan gagal
        Cache::forget($attemptsKey);
        Cache::forget($lockoutUntilKey);

        Auth::login($user, $request->filled('remember'));
 
        $request->session()->regenerate();
 
        return redirect()->route('dashboard');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
