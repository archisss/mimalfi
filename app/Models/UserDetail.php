<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'client_reference',
        'work_address',
        'payment_address',
        'aval',
        'picture',
        'picture_ine',
        'picture_domicilio',
        'picture_foto',
    ];
}
