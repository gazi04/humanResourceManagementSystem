<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\EmployeeRole, $this>
     */
    public function employeeRole(): BelongsTo
    {
        return $this->belongsTo(EmployeeRole::class, 'employeeID', 'employeeID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'roleID', 'roleID');
    }

    public function getRoleName(): string
    {
        return $this->employeeRole()->first()->role()->first()["roleName"];
    }
}
