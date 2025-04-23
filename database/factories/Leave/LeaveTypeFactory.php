<?php

namespace Database\Factories\Leave;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leave\LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $leaveTypes = [
            'Annual Leave',
            'Sick Leave',
            'Maternity Leave',
            'Paternity Leave',
            'Unpaid Leave',
            'Study Leave',
            'Bereavement Leave',
            'Compensatory Leave',
        ];

        $selectedType = $this->faker->unique()->randomElement($leaveTypes);

        // Determine if the leave type is paid based on its name
        $isPaid = !in_array($selectedType, ['Unpaid Leave']);

        return [
            'name' => $selectedType,
            'description' => $this->faker->sentence(),
            'isPaid' => $isPaid,
            'requiresApproval' => $this->faker->boolean(90), // 90% chance of requiring approval
            'isActive' => $this->faker->boolean(95), // 95% chance of being active
        ];
    }

    /**
     * Configure the model factory to create related records.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (\App\Models\Leave\LeaveType $leaveType) {
            // Create leave policy for this leave type
            $leaveType->policy()->create([
                'annualQuota' => $this->faker->numberBetween(5, 30),
                'maxConsecutiveDays' => $this->faker->numberBetween(5, 14),
                'allowHalfDay' => $this->faker->boolean(),
                'probationPeriodDays' => $this->faker->numberBetween(30, 180),
                'carryOverLimit' => $this->faker->randomFloat(2, 0, 10),
                'restricedDays' => json_encode($this->faker->randomElements(
                    ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    $this->faker->numberBetween(0, 2)
                )),
                'requirenments' => json_encode([
                    'minimumService' => $this->faker->numberBetween(0, 12) . ' months',
                    'documentation' => $this->faker->randomElement(['None', 'Medical Certificate', 'Manager Approval', 'HR Approval']),
                ]),
            ]);

            // Attach to random roles (assuming you have roles seeded)
            $roles = \App\Models\Role::inRandomOrder()
                ->take($this->faker->numberBetween(1, 4))
                ->pluck('roleID')
                ->toArray();

            $leaveType->roles()->attach($roles);
        });
    }}
