<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Loan extends Model
{
    use LogsActivity;

    protected $fillable = [
        'customer_id',
        'principal',
        'interest_rate',
        'tenure_months',
        'start_date',
        'end_date',
        'repayment_cycle',
        'status',
        'loan_balance',
        'repaid_principal',
        'overdue_payment',
        'total_obligation',
        'current_interest'
    ];

    protected $casts = [
        'principal' => 'float',
        'interest_rate' => 'float',
        'tenure_months' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'loan_balance' => 'float',
        'repaid_principal' => 'float',
        'overdue_payment' => 'float',
        'total_obligation' => 'float',
        'current_interest' => 'float'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('loan');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function schedules()
    {
        return $this->hasMany(RepaymentSchedule::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    // Scope for ongoing loans
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }
}
