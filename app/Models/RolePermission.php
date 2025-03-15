<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'rolePermissionID';

    protected $fillable = [
        'rolePermissionID',
        'roleID',
        'permissionID'
    ];
}
