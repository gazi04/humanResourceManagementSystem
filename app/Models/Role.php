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

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_roles', 'roleID', 'employeeID');
    }
}
