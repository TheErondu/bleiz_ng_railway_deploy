<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $loan;
    protected $referenceNumber;

    /**
     * Create a new notification instance.
     */
    public function __construct(Loan $loan, string $referenceNumber)
    {
        $this->loan = $loan;
        $this->referenceNumber = $referenceNumber;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $loan = $this->loan->load('repaymentSchedules');
        $customer = $loan->customer;
        $monthlyInterest = ($loan->principal * ($loan->interest_rate / 100)) / 12;
        $schedules = $loan->repaymentSchedules;

        return (new MailMessage)
            ->subject('Loan Approved - Ref: ' . $this->referenceNumber)
            ->view('emails.loan-approved', [
                'loan' => $loan,
                'customer' => $customer,
                'referenceNumber' => $this->referenceNumber,
                'monthlyInterest' => $monthlyInterest,
                'schedules' => $schedules,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'loan_id' => $this->loan->id,
            'reference_number' => $this->referenceNumber,
            'amount' => $this->loan->principal,
            'status' => 'approved',
            'message' => 'Your loan application of ₦' . number_format($this->loan->principal, 2) . ' has been approved!',
            'url' => route('customer.loans.show', $this->loan),
        ];
    }
}
