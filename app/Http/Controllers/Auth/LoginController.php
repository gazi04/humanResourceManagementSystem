<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginPage() { return view('Auth.Login'); }
    public function showSignupPage() { return view('Auth.SignUp'); }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('phone', 'password');

        if(Auth::attempt(['Phone' => $credentials['phone'], 'Password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'phone' => 'Numri i telefonit ose fjalëkalimi janë të gabuar.',
        ])->withInput($request->only('phone'));
    }

    public function register(RegisterRequest $request)
    {
        return 'register successfully';
    }
}
