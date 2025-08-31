<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RepaymentSchedule extends Model
{
    use LogsActivity;

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('repayment_shedule');
    }

    protected $fillable = [
        'loan_id', 'due_date', 'amount_due', 'amount_paid', 'status', 'paid_at'
    ];

    protected $casts = [
    'due_date' => 'date',
];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }
}
