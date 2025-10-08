<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelfBank extends Model
{
    // use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'amount',
        'bank_date'
    ];

    /**
     * Relación con el modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
