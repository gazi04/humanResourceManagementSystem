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
            'salary' => $this->faker->numberBetween(30000, 100000),
            'status' => $this->faker->randomElement(['Active', 'Inactive']),
            'departmentID' => $this->faker->numberBetween(3, 6),
            'supervisorID' => null, // Initially set to null
            'contractID' => $this->faker->numberBetween(1, 10), // Assuming 10 contracts exist
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
