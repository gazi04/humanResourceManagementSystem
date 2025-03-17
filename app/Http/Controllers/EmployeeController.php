<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeController extends Controller
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

    public function employeers(): View
    {
        return view('Admin.employee');
    }

    public function humanResources(): View
    {
        return view('Admin.hrEmploye');
    }

    public function administrators(): View
    {
        return view('Admin.admin');
    }

    public function managers(): View
    {
        return view('Admin.depMenager');
    }
}
