<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    protected $table = 'vacations';
    protected $primaryKey = 'vacationID';

    protected $fillable = [
        'vacationID',
        'employeeID',
        'leaveType',
        'startDate',
        'endDate',
        'status'
    ];
}
