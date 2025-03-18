<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(): RedirectResponse
    {
        /** @var Employee $user */
        $user = Auth::guard('employee')->user();

        return match ($user->getRoleName()) {
            'admin' => redirect()->route('admin.dashboard'),
            'hr' => redirect()->route('hr.dashboard'),
            'manager' => redirect()->route('manager.dashboard'),
            'employee' => redirect()->route('employee.dashboard'),
            default => abort(403),
        };
    }
}
