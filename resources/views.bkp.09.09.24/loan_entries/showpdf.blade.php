<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-size: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }

        table {
            width: 100%;
            min-width: 100%;
            max-width: 100%;
        }

        table,
        th,
        td {
            height: 16px;
            border: 1px solid #999999;
        }

        .page-break {
            page-break-after: always;
        }
    </style>

</head>

<body>
    <div class="card">
        <div class="card-body">
            <table style="border: none;" cellspacing="0" cellpadding="5">
                <tr>
                    <td style="border: none;"><img src="{{ url('img/logo.jpg') }}" style="height: 80px"></td>
                    <td style="border: none;"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Account Details
            </h3>
        </div>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr class="row">
                <td style="border: none;" class="col-5 col-sm-5 col-md-5">
                    <table cellspacing="0" cellpadding="5">
                        <tr>
                            <td>Account Number</td>
                            <td>{{ str_replace('LOAN', '', $loan_account->loan_id) }}</td>
                        </tr>
                        <tr>
                            <td>Sanction Limit</td>
                            <td><span
                                    style="font-family: DejaVu Sans; sans-serif;font-weight:normal">&#8377;</span>{{ number_format($loan_account->sanction_limit, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Bank Loan Date</td>
                            <td>{{ \Carbon\Carbon::parse($loan_account->bank_loan_date)->format('d M, Y') }}</td>
                        </tr>
                    </table>
                </td>
                <td style="border: none;" class="col-4 col-sm-4 col-md-4">
                    <table cellspacing="0" cellpadding="5" class="table table-responsive">
                        <tr>
                            <td>Bank Sanction Amount</td>
                            <td><span
                                    style="font-family: DejaVu Sans; sans-serif;font-weight:normal">&#8377;</span>{{ number_format($loan_account->bank_sanction_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>NFBC Sanction Amount</td>
                            <td><span
                                    style="font-family: DejaVu Sans; sans-serif;font-weight:normal">&#8377;</span>{{ number_format($loan_account->nbfc_sanction_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>NBFC Loan Date</td>
                            <td>{{ \Carbon\Carbon::parse($loan_account->nbfc_loan_date)->format('d M, Y') }}</td>
                        </tr>

                    </table>
                </td>
                <td style="border: none;" class="col-3 col-sm-3 col-md-3">
                    <table cellspacing="0" cellpadding="5" class="table table-responsive">

                        <tr>
                            <td>Bank ROI</td>
                            <td>{{ $setting->bank_interest }}%</td>
                        </tr>
                        <tr>
                            <td>NBFC ROI</td>
                            <td>{{ $setting->nbfc_interest }}%</td>
                        </tr>
                        <tr>
                            <td>Tenure</td>
                            <td>{{ $loan_account->loan_tenure }} Months</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Statement of Account ({{ isset($name) ? ucfirst($name) : 'Total' }})
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">

                    <div class="row brand-white-bg">


                        <table cellspacing="0" cellpadding="5" class="table table-stripped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Journal No.</th>
                                    <th>Debit (<span
                                            style="font-family: DejaVu Sans; sans-serif;font-weight:normal">&#8377;</span>)
                                    </th>
                                    <th>Credit (<span
                                            style="font-family: DejaVu Sans; sans-serif;font-weight:normal">&#8377;</span>)
                                    </th>
                                    <th>o/s. Principal (<span
                                            style="font-family: DejaVu Sans; sans-serif;font-weight:normal">&#8377;</span>)
                                    </th>
                                    <th>o/s. Interest (<span
                                            style="font-family: DejaVu Sans; sans-serif;font-weight:normal">&#8377;</span>)
                                    </th>
                                    <th>o/s. Balance (<span
                                            style="font-family: DejaVu Sans; sans-serif;font-weight:normal">&#8377;</span>)
                                    </th>
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


                    </div>

                </div>
            </div>
        </div>
    </div>
</body>

</html>
