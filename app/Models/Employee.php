<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Employee extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $fillable = [
        'EmployeeID',
        'FirstName',
        'LastName',
        'Email',
        'Password',
        'Phone',
        'HireDate',
        'JobTitle',
        'Salary',
        'Status',

        'DepartmentID',
        'SupervisorID',
        'ContractID',
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];
}
