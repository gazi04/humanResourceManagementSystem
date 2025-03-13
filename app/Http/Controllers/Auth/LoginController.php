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
        if(!Auth::guard('employee')->user()){
            return view('Auth.Login');
        }

        return back();
    }

    /* TODO- NEED TO TAKE INTO CONSIDERATION THAT AN USER MIGHT NOT HAVE A ROLE YET, SO THE SYSTEM SHOULD WORK IN THIS CASE ALSO  */
    /* HINT- AN SOLUTION TO THIS PROBLEM IS TO MAKE A DEFAULT ROLE LIKE `EMPLOYEE` FOR EACH USER THAT IS CREATED  */
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
