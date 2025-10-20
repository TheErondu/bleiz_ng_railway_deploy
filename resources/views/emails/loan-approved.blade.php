<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Approved</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .intro {
            background-color: #dbeafe;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #2563eb;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        .loan-details {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #4b5563;
            flex: 1;
        }
        .detail-value {
            font-weight: bold;
            color: #1f2937;
            text-align: right;
            flex: 1;
        }
        .repayment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .repayment-table th {
            background-color: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        .repayment-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .repayment-table tr:last-child td {
            border-bottom: none;
            font-weight: bold;
            background-color: #f3f4f6;
        }
        .conditions {
            background-color: #fef3c7;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
            border-left: 4px solid #f59e0b;
        }
        .conditions h3 {
            color: #92400e;
            margin-top: 0;
        }
        .conditions ol {
            margin: 10px 0;
            padding-left: 25px;
        }
        .conditions li {
            margin-bottom: 12px;
            color: #78350f;
        }
        .cta-button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 30px 0;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .highlight {
            color: #2563eb;
            font-weight: bold;
        }
        @media only screen and (max-width: 600px) {
            .container {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🏦 Bleiz Global Tech</div>
            <p style="color: #6b7280; margin: 0;">Smart Loans, Built for Nigerians</p>
        </div>

        <div class="greeting">
            Dear <strong>{{ $customer->user->name }}</strong>,
        </div>

        <div class="intro">
            <p style="margin: 0; font-size: 16px;">
                <strong>🎉 Congratulations!</strong> Further to your loan request, we are pleased to inform you that
                <strong>Bleiz Global Tech</strong> (hereinafter called "the Lender") has approved your loan facility
                subject to the following terms and conditions:
            </p>
        </div>

        <div class="section-title">📋 Loan Details</div>
        <div class="loan-details">
            <div class="detail-row">
                <div class="detail-label">Loan Reference:</div>
                <div class="detail-value highlight">{{ $referenceNumber }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Borrower:</div>
                <div class="detail-value">{{ $customer->user->name }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Borrower's Bank Account:</div>
                <div class="detail-value">{{ $customer->bank_account_number }}, {{ $customer->bank_name }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Lender:</div>
                <div class="detail-value">Bleiz Global Tech Ltd</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Loan Amount:</div>
                <div class="detail-value highlight">₦{{ number_format($loan->principal, 2) }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Interest Rate:</div>
                <div class="detail-value">{{ $loan->interest_rate }}% per annum</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Tenor:</div>
                <div class="detail-value">{{ $loan->tenure_months }} months</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Monthly Interest:</div>
                <div class="detail-value">₦{{ number_format($monthlyInterest, 2) }}</div>
            </div>
        </div>

        <div class="section-title">💰 Repayment Schedule</div>
        <table class="repayment-table">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Due Date</th>
                    <th>Repayment (₦)</th>
                    <th>Narration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schedules as $index => $schedule)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $schedule->due_date->format('d-M-y') }}</td>
                    <td>{{ number_format($schedule->amount_due, 2) }}</td>
                    <td>
                        @if($index < $loan->tenure_months - 1)
                            Interest
                        @else
                            Interest + Principal
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="text-align: right;"><strong>Total Repayment:</strong></td>
                    <td colspan="2"><strong>₦{{ number_format($loan->total_obligation, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="conditions">
            <h3>📌 OTHER CONDITIONS</h3>
            <ol>
                <li><strong>Early Liquidation:</strong> The Borrower is at liberty to pay off the loan before maturity date.</li>

                <li><strong>Principal Reduction:</strong> The Borrower may reduce the loan amount by paying part of the principal. Subsequent interest will be based on the outstanding principal amount.</li>

                <li><strong>Interest Charges:</strong> Interest on the principal cannot be prorated as full interest will be charged on the principal amount.</li>

                <li><strong>Default Clause:</strong> In the event of default and the loan becomes delinquent, the Lender shall have the right to report the delinquent loan to the Borrower's employer as well as other third-party agencies to recover any outstanding debt.</li>

                <li><strong>Renewal Terms:</strong> Renewal of the facility after maximum tenor shall be subject to a fresh request and based on satisfactory performance (100% liquidation and proper utilization).</li>
            </ol>
            <p style="margin-bottom: 0; font-weight: 600; color: #92400e;">
                ⚠️ The facility shall become available to the Borrower upon acceptance of these terms and conditions.
            </p>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('customer.loans.show', $loan) }}" class="cta-button">
                View Full Loan Details →
            </a>
        </div>

        <div class="footer">
            <p style="font-size: 16px; color: #2563eb; font-weight: 600; margin-bottom: 10px;">
                Thanks for choosing {{config('app.name')}} 🚀
            </p>
            <p style="margin: 5px 0;">
                For support, contact us at: <a href="mailto:support@bleiz.ng" style="color: #2563eb;">support@bleiz.ng</a>
            </p>
            <p style="margin: 5px 0; color: #9ca3af;">
                © {{ date('Y') }} {{config('app.name')}}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
