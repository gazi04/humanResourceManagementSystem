<?php

namespace App\Models;

use App\Models\Leave\LeaveBalance;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
        'status',

        'departmentID',
        'supervisorID',
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Contract, $this>
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough<\App\Models\Role, \App\Models\EmployeeRole, $this>
     */
    public function role(): HasOneThrough
    {
        return $this->hasOneThrough(
            Role::class,               // The target model we want to access (Role)
            EmployeeRole::class,       // The intermediate model (pivot table)
            'employeeID',              // Foreign key on the intermediate table
            'roleID',                  // Foreign key on the target table
            'employeeID',              // Local key on this model
            'roleID'                   // Local key on intermediate table
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Leave\LeaveBalance, $this>
     */
    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class, 'employeeID', 'employeeID');
    }

    public function getRoleName(): string
    {
        return $this->employeeRole()->first()->role()->first()['roleName'];
    }

    protected function casts(): array
    {
        return [
            'hireDate' => 'date',
        ];
    }
}
