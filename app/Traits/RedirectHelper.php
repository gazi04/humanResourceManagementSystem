<?php

namespace App\Traits;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait RedirectHelper
{
    public function toDashboard(Request $request): RedirectResponse
    {
        /** @var Employee $user */
        $user = Auth::guard('employee')->user();

        if ($user === null) { redirect()->route('loginPage'); }

        return match ($user->getRoleName()) {
            'admin' => redirect()->route('admin.dashboard'),
            'hr' => redirect()->route('hr.dashboard'),
            'manager' => redirect()->route('manager.dashboard'),
            'employee' => redirect()->route('employee.dashboard'),
            default => abort(403),
        };
    }
}
