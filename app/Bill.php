<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $guarded = []; 
    
    protected $casts = [
        'supplier_phone' => 'array'
    ];
    
    public function getImagePathAttribute()
    {
        return asset('uploads/purchaseInvoices_images/' . $this->image);
    }
}
