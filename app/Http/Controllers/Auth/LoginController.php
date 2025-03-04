<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginPage() { return view('Auth.Login'); }
    public function showSignupPage() { return view('Auth.SignUp'); }

    public function login(LoginRequest $request)
    {
        return redirect()->route('dashboard');
    }

    public function register(RegisterRequest $request)
    {
        return 'register successfully';
    }
}
