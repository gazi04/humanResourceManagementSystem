<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Traits\AuthHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Auth\SessionGuard;
use App\Models\Employee;

class LoginController extends Controller
{
    use AuthHelper;

    public function showLoginPage(): View
    {
        return view('Auth.Login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('phone', 'password');

        /** @var SessionGuard $guard */
        $guard = Auth::guard('employee');
        if($guard->attempt($credentials)) {
            $request->session()->regenerate();

            /** @var Employee $user */
            $user = Auth::guard('employee')->user();
            return match ($user->getRoleName()) {
                'admin' => redirect()->route('admin-dashboard'),
                'hr' => redirect()->route('hr-dashboard'),
                'manager' => redirect()->route('manager-dashboard'),
                'employee' => redirect()->route('employee-dashboard'),
                default => abort(403),
            };
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
