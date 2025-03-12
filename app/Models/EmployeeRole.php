<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRole extends Model
{
    protected $table = 'employee_roles';
    protected $primaryKey = 'employeeRoleID';

    protected $fillable = [
        'employeeID',
        'roleID'
    ];
}
