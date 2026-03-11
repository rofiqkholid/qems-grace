<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
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

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'Username not found',
            ])->withInput($request->only('username'));
        }

        $passwordMatch = ($user->password === $request->password) || Hash::check($request->password, $user->password);
        $epicorPasswordMatch = ($user->epicor_password === $request->password) || Hash::check($request->password, $user->epicor_password);

        if (!$passwordMatch && !$epicorPasswordMatch) {
            return back()->withErrors([
                'password' => 'Password incorrect',
            ])->withInput($request->only('username'));
        }

        Auth::login($user, $request->filled('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
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
