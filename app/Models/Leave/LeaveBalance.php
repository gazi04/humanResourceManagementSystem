<?php

namespace App\Models\Leave;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class LeaveBalance extends Model
{
    protected $table = 'leave_balances';

    protected $primaryKey = 'leaveBalanceID';

    protected $fillable = [
        'employeeID',
        'leaveTypeID',
        'remainingDays',
        'usedDays',
        'carriedOverDays',
        'year',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeID', 'employeeID');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leaveTypeID', 'leaveTypeID');
    }

    public function policy(): HasOneThrough
    {
        return $this->hasOneThrough(
            LeavePolicy::class,    // The distant/final model we want to access
            LeaveType::class,      // The intermediate model
            'leaveTypeID',         // Foreign key on the intermediate model (LeaveType)
            'leaveTypeID',         // Foreign key on the distant model (LeavePolicy)
            'leaveTypeID',         // Local key on this model (LeaveBalance)
            'leaveTypeID'          // Local key on the intermediate model (LeaveType)
        );
    }
}
