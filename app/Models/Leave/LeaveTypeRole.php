<?php

namespace App\Models\Leave;

use Illuminate\Database\Eloquent\Model;

class LeaveTypeRole extends Model
{
    protected $table = 'leave_type_role';

    protected $primaryKey = 'leaveTypeRoleID';

    protected $fillable = [
        'leaveTypeID',
        'roleID',
    ];
}
