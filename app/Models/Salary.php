<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'salaryID',
        'employeeID',
        'paymentDate',
        'amount'
    ];
}
