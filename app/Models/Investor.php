<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Investor extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Investor_transaction');
    }

    protected $fillable = ['user_id', 'amount_provided', 'date_contributed', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }
    public function totalWithdrawn()
    {
        return $this->withdrawals()->sum('amount');
    }

    public function accruedROI()
    {
        // this will be calculated based on portfolio logic later
        return $this->capital * ($this->roi_percentage / 100);
    }

    public function availableROI()
    {
        return $this->accruedROI() - $this->totalWithdrawn();
    }
}
