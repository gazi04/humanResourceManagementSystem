<?php

namespace Database\Factories\Leave;

use App\Models\Employee;
use App\Models\Leave\LeaveRequest;
use App\Models\Leave\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leave\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    // Align with database enum values
    const DURATION_FULL_DAY = 'fullDay';

    const DURATION_HALF_DAY = 'halfDay';

    const DURATION_MULTI_DAY = 'multiDay';

    protected $model = \App\Models\Leave\LeaveRequest::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+3 months');
        $durationType = $this->faker->randomElement([
            self::DURATION_FULL_DAY,
            self::DURATION_HALF_DAY,
            self::DURATION_MULTI_DAY,
        ]);

        // Calculate end date based on duration type
        $endDate = clone $startDate;
        if ($durationType === self::DURATION_MULTI_DAY) {
            $endDate->add(new \DateInterval('P'.$this->faker->numberBetween(1, 14).'D'));
        }

        return [
            'employeeID' => Employee::factory(),
            'leaveTypeID' => LeaveType::factory(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'durationType' => $durationType,
            'halfDayType' => $durationType === self::DURATION_HALF_DAY
                ? $this->faker->randomElement(['firstHalf', 'secondHalf']) // Match database enum
                : null,
            'requestedDays' => $this->calculateRequestedDays($durationType, $startDate, $endDate),
            'reason' => $this->faker->sentence(),
            'status' => 'pending',
            'rejectionReason' => null,
            'approvedBy' => null,
            'approvedAt' => null,
            'attachment' => null,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (LeaveRequest $leaveRequest) {
            // Ensure a leave balance exists for this employee and leave type
            \App\Models\Leave\LeaveBalance::firstOrCreate([
                'employeeID' => $leaveRequest->employeeID,
                'leaveTypeID' => $leaveRequest->leaveTypeID,
                'year' => now()->year,
            ],
                [
                    'remainingDays' => 20,
                    'usedDays' => 0,
                    'carriedOverDays' => 0,
                ]);
        });
    }

    /**
     * Indicate that the leave request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approvedBy' => null,
            'approvedAt' => null,
            'rejectionReason' => null,
        ]);
    }

    /**
     * Indicate that the leave request is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approvedBy' => Employee::factory(),
            'approvedAt' => $this->faker->dateTimeBetween($attributes['startDate'], 'now'),
            'rejectionReason' => null,
        ]);
    }

    /**
     * Indicate that the leave request is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'approvedBy' => Employee::factory(),
            'approvedAt' => $this->faker->dateTimeBetween($attributes['startDate'], 'now'),
            'rejectionReason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Calculate the number of requested days based on the date range and duration type.
     */
    protected function calculateRequestedDays(string $durationType, \DateTimeInterface $startDate, \DateTimeInterface $endDate): float
    {
        switch ($durationType) {
            case self::DURATION_HALF_DAY:
                return 0.5;
            case self::DURATION_FULL_DAY:
                return 1.0;
            case self::DURATION_MULTI_DAY: // Updated constant name
                return $endDate->diff($startDate)->days + 1;
            default:
                return 1.0;
        }
    }
}
