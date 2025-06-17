<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'), // Default password
            'phone' => $this->faker->regexify('(\+383|0)?[4-6][0-9]{7}'),
            'hireDate' => $this->faker->date,
            'jobTitle' => $this->faker->jobTitle,
            'status' => $this->faker->randomElement(['Active', 'Inactive']),
            'departmentID' => Department::inRandomOrder()->first(),
            'supervisorID' => null, // Initially set to null
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Employee $employee) {
            // Assign a random supervisor (excluding the employee itself)
            $supervisor = Employee::where('employeeID', '!=', $employee->employeeID)->inRandomOrder()->first();
            if ($supervisor) {
                $employee->supervisorID = $supervisor->employeeID;
                $employee->save();
            }
        });
    }

    /**
     * Assign a random role to the employee (or specific role if provided)
     */
    public function withRole(?string $roleName = null): static
    {
        return $this->afterCreating(function (Employee $employee) use ($roleName) {
            $role = $roleName
                ? Role::where('roleName', $roleName)->first()
                : Role::inRandomOrder()->first();

            if ($role) {
                EmployeeRole::updateOrCreate(
                    ['employeeID' => $employee->employeeID],
                    ['roleID' => $role->roleID]
                );
            }
        });
    }

    /**
     * Assign admin role
     */
    public function admin(): static
    {
        return $this->withRole('admin');
    }

    /**
     * Assign HR role
     */
    public function hr(): static
    {
        return $this->withRole('hr');
    }

    /**
     * Assign employee role
     */
    public function regularEmployee(): static
    {
        return $this->withRole('employee');
    }

    /**
     * Assign manager role
     */
    public function manager(): static
    {
        return $this->withRole('manager');
    }
}
