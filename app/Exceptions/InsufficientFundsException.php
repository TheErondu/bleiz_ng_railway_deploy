<?php
namespace App\Exceptions;

class InsufficientFundsException extends FinancialException
{
    public function __construct(float $requested, float $available)
    {
        $message = sprintf(
            'Insufficient funds. Requested: %s, Available: %s',
            format_currency($requested),
            format_currency($available)
        );

        parent::__construct($message);
    }
}
