<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\EmployeeService;
use Illuminate\View\View;

class HumanResourceController extends Controller
{
    public function __construct(protected EmployeeService $employeeService) {}

    public function index(): View
    {
        return view('Manager.dashboard');
    }
}
