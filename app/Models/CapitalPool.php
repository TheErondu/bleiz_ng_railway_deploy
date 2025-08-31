<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapitalPool extends Model
{
    protected $fillable = ['total_amount', 'available_amount'];

    public $timestamps = true;
}
