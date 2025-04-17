<?php

namespace App\Models\Leave;

use Illuminate\Database\Eloquent\Model;

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
}
