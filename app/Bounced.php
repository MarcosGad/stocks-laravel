<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bounced extends Model
{
    protected $guarded = []; 
    
    protected $casts = [
        'products' => 'array',
        'quantity' => 'array',
        'serial_numbers' => 'array'
    ];
}