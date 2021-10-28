<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    public function expediente() 
    {
        return $this->belongsTo('App\Models\Expediente');
    }
}
