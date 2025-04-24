<?php

namespace App\Models;

use App\Models\Leave\LeaveType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $primaryKey = 'roleID';

    protected $fillable = [
        'roleName',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\EmployeeRole, $this>
     */
    public function EmployeeRole(): BelongsTo
    {
        return $this->belongsTo(EmployeeRole::class, 'roleID', 'roleID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Employee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'roleID', 'roleID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Leave\LeaveType, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function leaveTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            LeaveType::class,           // Related model
            'leave_type_role',          // Pivot table name
            'roleID',                   // Foreign key on pivot table (refers to Role)
            'leaveTypeID'               // Related key on pivot table (refers to LeaveType)
        );
    }
}
