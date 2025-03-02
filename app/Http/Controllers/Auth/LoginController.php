<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginPage() { return view('Auth.LoginPage'); }

    public function login(LoginRequest $request)
    {
        return 'helo';
    }
}
