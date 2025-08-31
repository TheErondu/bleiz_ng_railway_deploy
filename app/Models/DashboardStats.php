<?php

namespace App\Models;

use Illuminate\Support\Collection;

class DashboardStats
{
    public $pool;
    public $totalCapital;
    public $idleCapital;
    public $loanPortfolio;
    public $totalWithdrawals;
    public $totalROI;
    public $availableROIPool;
    public $totalIdleFunds;
    public $currentROI;
    public $nextPotentialROI;
    public $realizedProfit;
    public $bleizProfit;
    public $investorStats;
    public $months;
    public $month;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}
