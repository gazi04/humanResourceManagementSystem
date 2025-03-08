<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Employee extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $fillable = [
        'employeeID',
        'firstName',
        'lastName',
        'email',
        'password',
        'phone',
        'hireDate',
        'jobTitle',
        'salary',
        'status',

        'departmentID',
        'supervisorID',
        'contractID',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
