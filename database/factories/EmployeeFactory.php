<?php

namespace Database\Factories;

use App\Models\Employee;
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
            'phone' => $this->faker->numerify('+###########'),
            'hireDate' => $this->faker->date,
            'jobTitle' => $this->faker->jobTitle,
            'status' => $this->faker->randomElement(['Active', 'Inactive']),
            'departmentID' => $this->faker->numberBetween(1, 4),
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
}
