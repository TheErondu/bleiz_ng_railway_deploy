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

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function repaymentSchedules()
    {
        return $this->hasMany(RepaymentSchedule::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    // Scopes
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeOverdue($query)
    {
        return $query->where('overdue_payment', '>', 0);
    }

    // Business Logic Methods
    public function getTotalPaidAttribute()
    {
        return $this->repayments()->sum('amount');
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->total_obligation - $this->getTotalPaidAttribute();
    }

    public function getPaymentProgressAttribute()
    {
        if ($this->total_obligation <= 0) return 0;
        return ($this->getTotalPaidAttribute() / $this->total_obligation) * 100;
    }

    public function isOverdue()
    {
        return $this->overdue_payment > 0;
    }

    public function getDaysOverdueAttribute()
    {
        $overdueSchedule = $this->schedules()
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->first();

        if (!$overdueSchedule) return 0;

        return $overdueSchedule->due_date->diffInDays(now());
    }

    public function getMonthlyPaymentAttribute()
    {
        if ($this->tenure_months <= 0) return 0;
        return $this->total_obligation / $this->tenure_months;
    }

    public function recordPayment(float $amount, string $paymentMethod, int $receivedBy)
    {
        // Find the earliest unpaid schedule
        $schedule = $this->schedules()
            ->where('status', 'pending')
            ->orWhere('status', 'overdue')
            ->orderBy('due_date')
            ->first();

        if (!$schedule) {
            throw new \Exception('No pending repayments found for this loan');
        }

        // Record the repayment
        $repayment = $this->repayments()->create([
            'repayment_schedule_id' => $schedule->id,
            'amount' => $amount,
            'paid_on' => now()->toDateString(),
            'payment_method' => $paymentMethod,
            'received_by' => $receivedBy
        ]);

        // Update schedule
        $schedule->increment('amount_paid', $amount);

        if ($schedule->amount_paid >= $schedule->amount_due) {
            $schedule->update([
                'status' => 'paid',
                'paid_at' => now()
            ]);
        }

        // Update loan balances
        $this->increment('repaid_principal', $amount);
        $this->decrement('loan_balance', $amount);

        if ($this->isOverdue()) {
            $this->decrement('overdue_payment', min($amount, $this->overdue_payment));
        }

        // Check if loan is fully paid
        if ($this->getRemainingBalanceAttribute() <= 0) {
            $this->update(['status' => 'completed']);
        }

        return $repayment;
    }
}
