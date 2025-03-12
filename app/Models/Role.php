<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'roleID';

    protected $fillable = [
        'roleName'
    ];

    public function EmployeeRole()
    {
        return $this->belongsTo(EmployeeRole::class, 'roleID', 'roleID');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'roleID', 'roleID');
    }
}
