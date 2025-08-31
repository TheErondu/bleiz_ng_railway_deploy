<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Repayment extends Model
{
    use LogsActivity;

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('repayment');
    }

    protected $fillable = [
        'repayment_schedule_id', 'amount', 'paid_on', 'payment_method', 'received_by'
    ];

    public function schedule()
    {
        return $this->belongsTo(RepaymentSchedule::class, 'repayment_schedule_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
