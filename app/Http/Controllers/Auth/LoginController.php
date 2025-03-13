<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Traits\AuthHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthHelper;

    public function showLoginPage()
    {
        /* TODO- NEED TO REDIRECT EACH USER BASED ON THEIR ROLE */
        /* HINT- USE MIDDLWARE TO ACHIEVE THAT  */
        if(!Auth::guard('employee')->user()){
            return view('Auth.Login');
        }

        return back();
    }

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
        return $this->logoutUser($request);
    }
}
