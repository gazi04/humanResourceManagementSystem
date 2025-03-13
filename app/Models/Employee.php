<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Employee extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'employees';
    protected $primaryKey = 'employeeID';

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

    public function employeeRole()
    {
        return $this->belongsTo(EmployeeRole::class, 'employeeID', 'employeeID');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'roleID', 'roleID');
    }

    public function getRoleName()
    {
        return $this->employeeRole()->first()->role()->first()["roleName"];
    }
}
