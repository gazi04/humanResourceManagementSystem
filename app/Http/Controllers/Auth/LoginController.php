<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginPage() { return view('Auth.Login'); }
    public function showSignupPage() { return view('Auth.SignUp'); }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('phone', 'password');

        if(Auth::guard('employee')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'phone' => 'Numri i telefonit ose fjalëkalimi janë të gabuar.',
        ])->withInput($request->only('phone'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out.');
    }
}
