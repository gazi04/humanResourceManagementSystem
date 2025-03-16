<?php

namespace App\Http\Controllers;

use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\DeleteDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function create(CreateDepartmentRequest $request)
    {
        $validated = $request->only('departmentName', 'supervisorID');
        Department::create($validated);

        return redirect()->route('admin-dashboard')->with('message', 'Departamenti është krijuar me sukses.');
    }

    public function delete(DeleteDepartmentRequest $request)
    {
        $validated = $request->only('departmentID');
        Department::where('departmentID', $validated['departmentID'])->delete();

        return redirect()->route('admin-dashboard')->with('message', 'Departamenti është fshirë me sukses.');
    }

    public function update(UpdateDepartmentRequest $request)
    {
        $validated = $request->only('departmentID', 'newDepartmentName');
        $department = Department::where('departmentID', $validated['departmentID'])->first();

        if ($department) {
            $department->departmentName = $validated['newDepartmentName'];
            $department->save();
        } else {
            return redirect()->route('admin-dashboard')->with('error', 'Departamenti nuk u gjet në bazën e të dhënave.');
        }

        return redirect()->route('admin-dashboard');
    }
}
