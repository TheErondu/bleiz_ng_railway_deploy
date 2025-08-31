<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = ['investor_id', 'amount', 'status', 'note', 'approved_at'];

    public function investor()
    {
        return $this->belongsTo(User::class, 'investor_id');
    }
}
