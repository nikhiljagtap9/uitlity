<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-size: 12px;
        }

        table {
            width: 100%;
            min-width: 100%;
            max-width: 100%;
        }

        table,
        th,
        td {
            height: 20px;
            border: 1px solid #999999;
        }

        .page-break {
            page-break-after: always;
        }
    </style>

</head>

<body>
    <table>
        <tr>
            <td>
                <table>
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td colspan="8">Account Details</td>
                                </tr>
                                <tr>
                                    <td>Account Number</td>
                                    <td>{{ str_replace('LOAN', 'B', $loan_account->loan_id) }}</td>
                                    <td></td>
                                    <td>Bank Sanction Amount</td>
                                    <td>₹{{ number_format($loan_account->bank_sanction_amount, 2) }}</td>
                                    <td></td>
                                    <td>Bank ROI</td>
                                    <td>{{ $setting->bank_interest }}%</td>
                                </tr>
                                <tr>
                                    <td>Sanction Limit</td>
                                    <td>₹{{ number_format($loan_account->sanction_limit, 2) }}</td>
                                    <td></td>
                                    <td>NFBC Sanction Amount</td>
                                    <td>₹{{ number_format($loan_account->nbfc_sanction_amount, 2) }}</td>
                                    <td></td>
                                    <td>NBFC Loan Date</td>
                                    <td>{{ \Carbon\Carbon::parse($loan_account->nbfc_loan_date)->format('d M, Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Bank Loan Date</td>
                                    <td>{{ \Carbon\Carbon::parse($loan_account->bank_loan_date)->format('d M, Y') }}
                                    </td>
                                    <td></td>
                                    <td>NBFC ROI</td>
                                    <td>{{ $setting->nbfc_interest }}%</td>
                                    <td></td>
                                    <td>Tenure</td>
                                    <td>{{ $loan_account->loan_tenure }} Months</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                &nbsp;
            </td>
        </tr>
        <tr>
            <table>
                <tr>
                    <td>
                        <table class="table table-stripped table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="8">Statement of Account
                                        ({{ isset($name) ? ucfirst($name) : 'Total' }})
                                    </th>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Journal No.</th>
                                    <th>Debit (₹)</th>
                                    <th>Credit (₹)</th>
                                    <th>o/s. Principal (₹)</th>
                                    <th>o/s. Interest (₹)</th>
                                    <th>o/s. Balance (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $debit = $credit = $balance = $principal_bank = $interest_bank = 0;
                                    $principal_nbfc = $interest_nbfc = $principal = $interest = 0;
                                @endphp
                                @foreach ($loan_entries as $loan)
                                    @php
                                        if (isset($name)) {
                                            if ($name == 'bank') {
                                                $debit = $debit + $loan->bank_debit;
                                                $credit = $credit + $loan->bank_credit;
                                                $balance = $balance + $loan->bank_balance;
                                                $balance1 = $loan->bank_balance;
                                                if ($loan->principal_bank_balance) {
                                                    $principal = $loan->principal_bank_balance;
                                                }
                                                if ($loan->interest_bank_balance) {
                                                    $interest = $loan->interest_bank_balance;
                                                }
                                            }
                                            if ($name == 'nbfc') {
                                                $debit = $debit + $loan->nbfc_debit;
                                                $credit = $credit + $loan->nbfc_credit;
                                                $balance = $balance + $loan->nbfc_balance;
                                                $balance1 = $loan->nbfc_balance;
                                                if ($loan->principal_nbfc_balance) {
                                                    $principal = $loan->principal_nbfc_balance;
                                                }
                                                if ($loan->interest_nbfc_balance) {
                                                    $interest = $loan->interest_nbfc_balance;
                                                }
                                            }
                                        } else {
                                            $debit = $debit + $loan->total_debit;
                                            $credit = $credit + $loan->total_credit;
                                            $balance = $balance + $loan->balance;
                                            $balance1 = $loan->balance;
                                            //if ($loan->principal_balance) {
                                            $principal = $loan->principal_balance;
                                            //}
                                            //if ($loan->interest_balance) {
                                            $interest = $loan->interest_balance;
                                            //}
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($loan->entry_date)->format('d M, Y') }}</td>
                                        <td>{{ $loan->description }}</td>
                                        <td>{{ str_replace('UTR', '', $loan->jnl_no) }}</td>
                                        @if (isset($name))
                                            @if ($name == 'bank')
                                                <td align="right">
                                                    {{ $loan->bank_debit ? number_format($loan->bank_debit, 2) : '-' }}
                                                </td>
                                                <td align="right">
                                                    {{ $loan->bank_credit ? number_format($loan->bank_credit, 2) : '-' }}
                                                </td>
                                                <td align="right">
                                                    {{ number_format($loan->principal_bank_balance, 2) }}
                                                </td>
                                                <td align="right">{{ number_format($loan->interest_bank_balance, 2) }}
                                                </td>
                                                <td align="right">{{ number_format($loan->bank_balance, 2) }}</td>
                                            @endif
                                            @if ($name == 'nbfc')
                                                <td align="right">
                                                    {{ $loan->nbfc_debit ? number_format($loan->nbfc_debit, 2) : '-' }}
                                                </td>
                                                <td align="right">
                                                    {{ $loan->nbfc_credit ? number_format($loan->nbfc_credit, 2) : '-' }}
                                                </td>
                                                <td align="right">
                                                    {{ number_format($loan->principal_nbfc_balance, 2) }}
                                                </td>
                                                <td align="right">{{ number_format($loan->interest_nbfc_balance, 2) }}
                                                </td>
                                                <td align="right">{{ number_format($loan->nbfc_balance, 2) }}</td>
                                            @endif
                                        @else
                                            <td align="right">
                                                {{ $loan->total_debit ? number_format($loan->total_debit, 2) : '-' }}
                                            </td>
                                            <td align="right">
                                                {{ $loan->total_credit ? number_format($loan->total_credit, 2) : '-' }}
                                            </td>
                                            <td align="right">{{ number_format($loan->principal_balance, 2) }}</td>
                                            <td align="right">{{ number_format($loan->interest_balance, 2) }}</td>
                                            <td align="right">{{ number_format($loan->balance, 2) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th style="text-align: right">{{ number_format($debit, 2) }}</th>
                                    <th style="text-align: right">{{ number_format($credit, 2) }}</th>
                                    <th style="text-align: right">{{ number_format($principal, 2) }}</th>
                                    <th style="text-align: right">{{ number_format($interest, 2) }}</th>
                                    <th style="text-align: right">{{ number_format($balance1, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
            </table>
        </tr>
    </table>
</body>

</html>
