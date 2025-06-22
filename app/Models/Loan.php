<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'alias',
        'amount',
        'interest',
        'total_to_pay',
        'payment_date',
        'payment_type',
        'payment_time',
        'use_lender',
        'term',
        'collector',
        'user_id',
        'loan_type_id',
        'use_bank',
        'status',
    ];

    public function user(){
        return $this->belongsTo(\App\Models\User::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    public function loanType()
    {
        return $this->belongsTo(\App\Models\LoanType::class);
    }
}
