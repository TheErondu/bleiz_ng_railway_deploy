<?php
namespace App\Exceptions;

class LoanNotFoundException extends FinancialException
{
    public function __construct(int $loanId)
    {
        $message = "Loan with ID {$loanId} not found or access denied.";
        parent::__construct($message, 404);
    }
}
