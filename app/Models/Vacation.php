<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    protected $fillable = [
        'VacationID',
        'EmployeeID',
        'LeaveType',
        'StartDate',
        'EndDate',
        'Status'
    ];
}
