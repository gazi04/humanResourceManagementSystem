<?php

namespace App\Models\Leave;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeavePolicy extends Model
{
    protected $table = 'leave_policies';

    protected $primaryKey = 'leavePolicyID';

    protected $fillable = [
        'leaveTypeID',
        'annualQuota',
        'maxConsecutiveDays',
        'allowHalfDay',
        'probationPeriodDays',
        'carryOverLimit',
        'restricedDays',
        'requirenments',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Leave\LeaveType, $this>
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
}
