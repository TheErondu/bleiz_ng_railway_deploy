<?php

// app/Exceptions/Handler.php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log channels.
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log financial operation errors with higher priority
            if ($this->isFinancialOperation($e)) {
                Log::channel('financial')->error('Financial operation failed', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'user_id' => auth()->id(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'url' => request()->fullUrl(),
                    'input' => $this->sanitizeFinancialInput(request()->all()),
                ]);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle API requests
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        // Handle financial operation errors
        if ($this->isFinancialOperation($e)) {
            return $this->handleFinancialError($request, $e);
        }

        return parent::render($request, $e);
    }

    protected function renderHttpException(HttpExceptionInterface $e)
    {
        if ($e->getStatusCode() == 419) {
            return redirect()->guest(route('login'))
                ->with('status', 'Session expired. Please login again.');
        }

        return parent::renderHttpException($e);
    }

    /**
     * Handle API exceptions with consistent format.
     */
    protected function handleApiException(Request $request, Throwable $e)
    {
        $status = 500;
        $message = 'Internal Server Error';

        if ($e instanceof HttpException) {
            $status = $e->getStatusCode();
            $message = $e->getMessage();
        } elseif ($e instanceof ValidationException) {
            $status = 422;
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], $status);
        }

        return response()->json([
            'success' => false,
            'message' => config('app.debug') ? $e->getMessage() : $message,
            'error_code' => $status
        ], $status);
    }

    /**
     * Handle financial operation errors with user-friendly messages.
     */
    protected function handleFinancialError(Request $request, Throwable $e)
    {
        $message = 'A financial operation error occurred. Please try again or contact support.';

        // Provide specific messages for common financial errors
        if (strpos($e->getMessage(), 'Insufficient funds') !== false) {
            $message = 'Insufficient funds in capital pool. Please contact administrator.';
        } elseif (strpos($e->getMessage(), 'Loan not found') !== false) {
            $message = 'The requested loan could not be found.';
        } elseif (strpos($e->getMessage(), 'Payment failed') !== false) {
            $message = 'Payment processing failed. Please verify your payment details and try again.';
        }

        return back()
            ->withInput($this->sanitizeFinancialInput($request->all()))
            ->withErrors(['financial_error' => $message]);
    }

    /**
     * Determine if the exception is related to financial operations.
     */
    protected function isFinancialOperation(Throwable $e): bool
    {
        $financialKeywords = [
            'loan',
            'payment',
            'repayment',
            'capital',
            'investor',
            'withdrawal',
            'transaction',
            'balance',
            'fund'
        ];

        $trace = strtolower($e->getTraceAsString());

        foreach ($financialKeywords as $keyword) {
            if (strpos($trace, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitize financial input for logging (remove sensitive data).
     */
    protected function sanitizeFinancialInput(array $input): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'card_number', 'cvv', 'pin'];

        foreach ($sensitiveFields as $field) {
            if (isset($input[$field])) {
                $input[$field] = '[REDACTED]';
            }
        }

        return $input;
    }
}
