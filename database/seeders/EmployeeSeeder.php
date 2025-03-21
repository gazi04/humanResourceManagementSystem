<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Create employees without a supervisorID
        $employees = Employee::factory()->count(100)->create();

        // Step 2: Assign supervisorID to each employee
        $employees->each(function ($employee) use ($employees) {
            // Assign a random supervisor (excluding the employee itself)
            $supervisor = $employees->where('employeeID', '!=', $employee->employeeID)->random();
            if ($supervisor) {
                $employee->supervisorID = $supervisor->employeeID;
                $employee->save();
            }

            $roles = Role::all(); // Get all roles from the roles table

            $employees->each(function ($employee) use ($roles) {
                // Assign a random role to the employee
                $role = $roles->random();
                EmployeeRole::create([
                    'employeeID' => $employee->employeeID,
                    'roleID' => $role->roleID,
                ]);
            });
        });
    }
}
