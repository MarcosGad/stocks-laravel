<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Representative extends Model
{
    protected $guarded = [];

    protected $casts = [
        'phone' => 'array'
    ];

    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }
}