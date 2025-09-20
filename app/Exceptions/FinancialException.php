<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class FinancialException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        Log::channel('financial')->critical('Financial Exception: ' . $this->getMessage(), [
            'exception' => static::class,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'user_id' => auth()->id(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
                'error_type' => 'financial_error'
            ], 400);
        }

        return back()
            ->withErrors(['financial_error' => $this->getMessage()])
            ->with('error', 'Financial operation failed: ' . $this->getMessage());
    }
}
