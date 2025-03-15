<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'contracts';
    protected $primaryKey = 'contractID';

    protected $fillable = [
        'contractID',
        'employeeID',
        'filePath',
        'uploadDate',
    ];
}
