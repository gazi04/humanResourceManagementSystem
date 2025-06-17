<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Create employees without a supervisorID
        $employees = Employee::factory()->count(40)->create();
        $roles = Role::all();

        // Step 2: Assign supervisorID and roles to each employee
        $employees->each(function ($employee) use ($employees, $roles) {
            // Assign a random supervisor (excluding the employee itself)
            $supervisor = $employees->where('employeeID', '!=', $employee->employeeID)->random();
            if ($supervisor) {
                $employee->supervisorID = $supervisor->employeeID;
                $employee->save();
            }

            // Assign a random role to the employee
            $role = $roles->random();
            EmployeeRole::firstOrCreate([
                'employeeID' => $employee->employeeID,
            ], [
                'roleID' => $role->roleID,
            ]);
        });
    }
}
