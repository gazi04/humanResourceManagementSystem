<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employeeID' => Employee::factory(),
            'contractPath' => 'contracts/' . Str::random(40) . '.pdf',
        ];
    }

    /**
     * Set specific employee.
     */
    public function forEmployee(Employee $employee): static
    {
        return $this->state(fn (array $attributes) => [
            'employeeID' => $employee->employeeID,
        ]);
    }

    /**
     * Set specific contract path.
     */
    public function withPath(string $path): static
    {
        return $this->state(fn (array $attributes) => [
            'contractPath' => $path,
        ]);
    }
}
