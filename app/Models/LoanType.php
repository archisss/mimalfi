<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    protected $fillable = [
        'name',
        'calendar_days',
        'payments_total',
        'porcentage',
    ];
}
