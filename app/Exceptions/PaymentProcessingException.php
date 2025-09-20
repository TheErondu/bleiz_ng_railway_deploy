<?php
namespace App\Exceptions;

class PaymentProcessingException extends FinancialException
{
    public function __construct(string $reason = "Payment processing failed")
    {
        parent::__construct($reason, 422);
    }
}
