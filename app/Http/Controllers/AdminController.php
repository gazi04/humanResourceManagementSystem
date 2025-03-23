<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\EmployeeService;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(protected EmployeeService $employeeService) {}

    public function index(): View
    {
        $roleID = Role::query()
            ->where('roleName', 'admin')
            ->value('roleID');

        $result = $this->employeeService->selectEmployeesBasedOnRoles($roleID);

        return view('Admin.admin', ['admins' => $result]);
    }
}
