<?php

namespace App\Models\Leave;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class LeaveRequest extends Model
{
    protected $table = 'leave_requests';

    protected $primaryKey = 'leaveRequestID';

    protected $fillable = [
        'employeeID',
        'leaveTypeID',
        'startDate',
        'endDate',
        'durationType',
        'halfDayType',
        'requestedDays',
        'reason',
        'status',
        'approvedBy',
        'approvedAt',
        'rejectionReason',
        'attachments',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeID', 'employeeID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Leave\LeaveType, $this>
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leaveTypeID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Employee, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approvedBy', 'employeeID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Leave\LeaveBalance, $this>
     */
    public function leaveBalance(): HasOne
    {
        return $this->hasOne(LeaveBalance::class)
            ->where('employeeID', $this->employeeID)
            ->where('leaveTypeID', $this->leaveTypeID);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough<\App\Models\Leave\LeavePolicy, \App\Models\Leave\LeaveType, $this>
     */
    public function policy(): HasOneThrough
    {
        return $this->hasOneThrough(
            LeavePolicy::class, // The final model we want to access
            LeaveType::class,   // The intermediate model
            'leaveTypeID',     // Foreign key on the LeaveRequest model (connecting to LeaveType)
            'leaveTypeID',     // Foreign key on the LeaveType model (connecting to LeavePolicy)
            'leaveTypeID',     // Local key on the LeaveRequest model
            'leaveTypeID'      // Local key on the LeaveType model
        );
    }

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'startDate' => 'date',
            'endDate' => 'date',
            'approvedAt' => 'datetime',
        ];
    }
}
