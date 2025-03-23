<?php

namespace App\Http\Controllers;

use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\DeleteDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Requests\SearchRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Services\DepartmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(protected DepartmentService $departmentServices, private readonly Redirector $redirector) {}

    public function index(): View
    {
        $result = $this->departmentServices->showDepartments();

        return view('Admin.departments', ['departments' => $result]);
    }

    public function store(CreateDepartmentRequest $request): RedirectResponse
    {
        $validated = $request->only('departmentName', 'supervisorID');
        $this->departmentServices->createDepartment($validated);

        return $this->redirector->route('admin.department.index')->with('success', 'Departamenti është krijuar me sukses.');
    }

    public function destroy(DeleteDepartmentRequest $request): RedirectResponse
    {
        $validated = $request->only('departmentID');
        $department = Department::query()->where('departmentID', $validated['departmentID'])->first();

        if (! $department) {
            return $this->redirector->route('admin.department.index')->with('error', 'Departamenti nuk u gjet në bazën e të dhënave.');
        }

        $this->departmentServices->deleteDepartment($department);

        return $this->redirector->route('admin.department.index')->with('success', 'Departamenti është fshirë me sukses.');
    }

    public function update(UpdateDepartmentRequest $request): RedirectResponse
    {
        $validated = $request->only('departmentID', 'newDepartmentName');
        $department = Department::query()->where('departmentID', $validated['departmentID'])->first();

        if (! $department) {
            return $this->redirector->route('admin.department.index')->with('error', 'Departamenti me'.$validated['departmentID'].' nuk u gjet në bazën e të dhënave.');
        }

        if ($request->has('newSupervisorID')) {
            $validatedSupervisorID = $request->only('newSupervisorID');
            /** @var Employee $employee */
            $employee = Employee::where('employeeID', $validatedSupervisorID['newSupervisorID'])->first();

            if (! $employee) {
                return $this->redirector->route('admin.department.index')->with('error', 'Nuk ka asnjë punonjës me ID '.$validatedSupervisorID['newSupervisorID'].' në bazën e të dhënave.');
            }

            if ($employee->getRoleName() != 'manager') {
                return $this->redirector->route('admin.department.index')->with('error', 'Punonjësi i përzgjedhur nuk është menaxher.');
            }

            $this->departmentServices->updateDepartment($department, [
                'departmentName' => $validated['newDepartmentName'],
                'supervisorID' => $validatedSupervisorID['newSupervisorID'],
            ]);
        } else {
            $this->departmentServices->updateDepartment($department, ['departmentName' => $validated['newDepartmentName']]);
        }

        return $this->redirector->route('admin.department.index')->with('success', 'Të dhënat e departamentit janë përditësuar me sukses.');
    }

    public function search(SearchRequest $request)
    {
        $validated = $request->only('searchingTerm');
        $result = $this->departmentServices->searchDepartment($validated['searchingTerm']);

        return view('Admin.departments', ['departments' => $result]);
    }
}
