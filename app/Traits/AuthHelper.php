<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait AuthHelper
{
    /**
     * Log out the user and invalidate the session.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logoutUser(Request $request): RedirectResponse
    {
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('loginPage')->with('success', 'You have been logged out.');
    }
}
