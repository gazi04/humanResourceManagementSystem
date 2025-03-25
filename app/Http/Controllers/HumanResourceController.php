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
        /*$roleID = Role::query()*/
        /*    ->where('roleName', 'hr')*/
        /*    ->value('roleID');*/
        /**/
        /*$result = $this->employeeService->selectEmployeesBasedOnRoles($roleID);*/
        /**/
        return view('Manager.dashboard');
    }
}
