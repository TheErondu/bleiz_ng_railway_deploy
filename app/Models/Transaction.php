<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;
class Transaction extends Model
{
    use LogsActivity;

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('transaction');
    }

    protected $fillable = [
        'type', 'reference_id', 'amount', 'description', 'date', 'user_id'
    ];

    public static function generateReferenceId(string $prefix = 'TX'): string
{
    $datePart = now()->format('Ymd');
    $randomPart = strtoupper(Str::random(6)); // alphanumeric random string
    return "{$prefix}-{$datePart}-{$randomPart}";
}


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
