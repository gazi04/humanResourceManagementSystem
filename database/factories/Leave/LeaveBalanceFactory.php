<?php

namespace Database\Factories\Leave;

use App\Models\Employee;
use App\Models\Leave\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leave\LeaveBalance>
 */
class LeaveBalanceFactory extends Factory
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
            'leaveTypeID' => LeaveType::factory(),
            'remainingDays' => $this->faker->numberBetween(0, 30),
            'usedDays' => $this->faker->numberBetween(0, 15),
            'carriedOverDays' => $this->faker->numberBetween(0, 5),
            'year' => $this->faker->year(),
        ];
    }

    /**
     * Configure the factory to create relationships.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (\App\Models\Leave\LeaveBalance $balance) {
            // Ensure the leave type has a policy if not already exists
            if (! $balance->leaveType->policy) {
                $balance->leaveType->policy()->create([
                    'annualQuota' => $this->faker->numberBetween(15, 30),
                    'maxConsecutiveDays' => $this->faker->numberBetween(5, 14),
                    'allowHalfDay' => $this->faker->boolean(),
                    'probationPeriodDays' => $this->faker->numberBetween(30, 180),
                    'carryOverLimit' => $this->faker->randomFloat(2, 0, 10),
                ]);
            }
        });
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
     * Set specific leave type.
     */
    public function forLeaveType(LeaveType $leaveType): static
    {
        return $this->state(fn (array $attributes) => [
            'leaveTypeID' => $leaveType->leaveTypeID,
        ]);
    }

    /**
     * Set specific year.
     */
    public function forYear(int $year): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => $year,
        ]);
    }
}
