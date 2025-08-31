<?php
namespace App\Services;

use App\Models\CapitalPool;

class CapitalPoolService {

     public function getPool(): CapitalPool|null
    {
        return CapitalPool::first(); // assuming one row
    }

    public function addFunds(float $amount): void
    {
        $pool = $this->getPool();
        $pool->increment('total_amount', $amount);
        $pool->increment('available_amount', $amount);
    }

    public function disburseLoan(float $amount): bool
    {
        $pool = $this->getPool();

        if ($pool->available_amount >= $amount) {
            $pool->decrement('available_amount', $amount);
            return true;
        }

        return false;
    }

    public function receiveRepayment(float $amount): void
    {
        $pool = $this->getPool();
        $pool->increment('available_amount', $amount);
    }

}
