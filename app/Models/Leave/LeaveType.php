<?php

namespace App\Models\Leave;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeaveType extends Model
{
    use HasFactory;

    protected $table = 'leave_types';

    protected $primaryKey = 'leaveTypeID';

    protected $fillable = [
        'name',
        'description',
        'isPaid',
        'requiresApproval',
        'isActive',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Leave\LeavePolicy, $this>
     */
    public function policy(): HasOne
    {
        return $this->hasOne(LeavePolicy::class, 'leaveTypeID', 'leaveTypeID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Role, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,           // Related model
            'leave_type_role',     // Pivot table name
            'leaveTypeID',         // Foreign key on pivot table (refers to LeaveType)
            'roleID'               // Related key on pivot table (refers to Role)
        );
    }
}
