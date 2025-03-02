<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRole extends Model
{
    protected $fillable = [
        'EmployeeRoleID',
        'EmployeeID',
        'RoleID'
    ];
}
