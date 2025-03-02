<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
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
}
