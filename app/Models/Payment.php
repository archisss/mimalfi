<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'loan_id',
        'payment_due',
        'collector',
        'amount',
        'pay_full',
        'payment_date',
        'payment_due',
    ];

    public function loan()
    {
        return $this->belongsTo(\App\Models\Loan::class);
    }

    protected static function booted()
    {
        static::created(function ($payment) {
            $loan = $payment->loan;

            if (!$loan || !$loan->loanType) {
                return;
            }

            $pagosActuales = $loan->payments()->count();
            $pagosTotales = $loan->loanType->payments_total;

            if ($pagosActuales >= $pagosTotales && $loan->status !== 'finalizado') {
                $loan->status = 'finalizado';
                $loan->save();
            }
        });
    }

}
