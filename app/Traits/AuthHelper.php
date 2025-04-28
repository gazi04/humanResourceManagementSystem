<?php

namespace App\Traits;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait AuthHelper
{
    /**
     * Log out the user and invalidate the session.
     */
    public function logoutUser(Request $request): RedirectResponse
    {
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('loginPage')->with('success', 'You have been logged out.');
    }

    public function getLoggedUserID(): int
    {
        /** @var Employee $loggedUser */
        $loggedUser = Auth::guard('employee')->user();
        return $loggedUser->employeeID;
    }
}
