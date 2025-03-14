<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeRole extends Model
{
    protected $table = 'employee_roles';
    protected $primaryKey = 'employeeRoleID';

    protected $fillable = [
        'employeeID',
        'roleID'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Employee, $this>
     */
    public function employee(): HasMany
    {
        return $this->hasMany(Employee::class, 'employeeID', 'employeeID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Role, $this>
     */
    public function role(): HasMany
    {
        return $this->hasMany(Role::class, 'roleID', 'roleID');
    }
}
