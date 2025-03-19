<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table = 'salaries';

    protected $primaryKey = 'salaryID';

    protected $fillable = [
        'salaryID',
        'employeeID',
        'paymentDate',
        'amount',
    ];
}
