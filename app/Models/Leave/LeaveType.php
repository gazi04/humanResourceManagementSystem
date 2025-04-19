<?php

namespace App\Models\Leave;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeaveType extends Model
{
    protected $table = 'leave_types';

    protected $primaryKey = 'leaveTypeID';

    protected $fillable = [
        'name',
        'description',
        'isPaid',
        'requiresApproval',
        'isActive',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Leave\LeavePolicy, $this>
     */
    public function policy(): HasOne
    {
        return $this->hasOne(LeavePolicy::class);
    }
}
