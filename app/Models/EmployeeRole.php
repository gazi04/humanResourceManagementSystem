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

    public function employee()
    {
        return $this->hasMany(Employee::class, 'employeeID', 'employeeID');
    }

    public function role()
    {
        return $this->hasMany(Role::class, 'roleID', 'roleID');
    }
}
