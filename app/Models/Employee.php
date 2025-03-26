<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model implements AuthenticatableContract
{
    use Authenticatable, HasFactory;

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentID', 'departmentID');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'employeeID', 'employeeID');
    }

    public function getRoleName(): string
    {
        return $this->employeeRole()->first()->role()->first()['roleName'];
    }
}
