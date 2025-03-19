<?php

namespace App\Http\Controllers;

use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\DeleteDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(protected DepartmentService $departmentServices, private readonly Redirector $redirector) {}

    public function index(): View
    {
        return view('Admin.departments');
    }

    public function store(CreateDepartmentRequest $request): RedirectResponse
    {
        $validated = $request->only('departmentName', 'supervisorID');
        $this->departmentServices->createDepartment($validated);

        return $this->redirector->route('admin.dashboard')->with('message', 'Departamenti është krijuar me sukses.');
    }

    public function destroy(DeleteDepartmentRequest $request): RedirectResponse
    {
        $validated = $request->only('departmentID');
        $department = Department::query()->where('departmentID', $validated['departmentID'])->first();

        if (!$department) {
            return $this->redirector->route('admin.dashboard')->with('error', 'Departamenti nuk u gjet në bazën e të dhënave.');
        }

        $this->departmentServices->deleteDepartment($department);

        return $this->redirector->route('admin.dashboard')->with('message', 'Departamenti është fshirë me sukses.');
    }

    public function update(UpdateDepartmentRequest $request): RedirectResponse
    {
        $validated = $request->only('departmentID', 'newDepartmentName');
        $department = Department::query()->where('departmentID', $validated['departmentID'])->first();

        if (!$department) {
            return $this->redirector->route('admin.dashboard')->with('error', 'Departamenti nuk u gjet në bazën e të dhënave.');
        }

        $this->departmentServices->updateDepartment($department, ['departmentName' => $validated['newDepartmentName']]);

        return $this->redirector->route('admin.dashboard');
    }
}
