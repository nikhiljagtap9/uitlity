@extends('layout.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Account Details
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-5 col-sm-5 col-md-5">
                    <table class="table table-responsive">
                        <tr>
                            <td>Account Number</td>
                            <td>{{ str_replace('LOAN', '', $loan_account->loan_id) }}</td>
                        </tr>
                        <tr>
                            <td>Sanction Limit</td>
                            <td>₹{{ number_format($loan_account->sanction_limit, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Bank Loan Date</td>
                            <td>{{ \Carbon\Carbon::parse($loan_account->bank_loan_date)->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <td>NBFC Loan Date</td>
                            <td>{{ \Carbon\Carbon::parse($loan_account->nbfc_loan_date)->format('d M, Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-4 col-sm-4 col-md-4">
                    <table class="table table-responsive">
                        <tr>
                            <td>Bank Sanction Amount</td>
                            <td>₹{{ number_format($loan_account->bank_sanction_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>NFBC Sanction Amount</td>
                            <td>₹{{ number_format($loan_account->nbfc_sanction_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Loan Closure Date</td>
                            <td>{{ $closureDate->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <td>Repayment Schedule</td>
                            <td>{{ $loan_account->Repayment_Type }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-3 col-sm-3 col-md-3">
                    <table class="table table-responsive">

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
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Repayment Schedule
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">

                    <div class="row brand-white-bg">


                        <table class="table table-stripped table-bordered">
                            <thead>
                                <tr>
                                    <th>Loan Account No</th>
                                    <th>Sanction Limit</th>
                                    <th>Bank Amount</th>
                                    <th>NBFC Amount</th>
                                    <th>Bank Interest</th>
                                    <th>MFL Interest</th>
                                    <th>Tenure (Month)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ str_replace('LOAN', '', $loan_account->loan_id) }}</td>
                                    <td>₹{{ number_format($loan_account->sanction_limit, 2) }}</td>
                                    <td>₹{{ number_format($loan_account->bank_sanction_amount, 2) }}</td>
                                    <td>₹{{ number_format($loan_account->nbfc_sanction_amount, 2) }}</td>
                                    <td>{{ number_format($loan_account->bank_interest, 2) }}%</td>
                                    <td>{{ number_format($loan_account->nbfc_interest, 2) }}%</td>
                                    <td>{{ $loan_account->loan_tenure }}</td>


                                </tr>
                            </tbody>
                        </table>


                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Bank
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">

                    <div class="row brand-white-bg">


                        <table class="table table-stripped table-bordered">
                            <thead>
                                <tr>
                                    <th>Inatallment No</th>
                                    <th>Loan Date</th>
                                    <th>Outstanding Principle</th>
                                    <th>Principle Received</th>
                                    <th>Total Interest</th>
                                    <th>Interest Received</th>
                                    <th>Repayment Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>{{ \Carbon\Carbon::parse($loan_account->bank_loan_date)->format('d M, Y') }}</td>
                                    <td>₹{{ number_format($loan_account->bank_sanction_amount, 2) }}</td>
                                    <td>₹{{ number_format($loan_account->bank_sanction_amount, 2) }}</td>
                                    <td>
                                        ₹{{ number_format($bank_interest, 2) }}
                                    </td>
                                    <td>
                                        ₹{{ number_format($bank_interest, 2) }}
                                    </td>
                                    <td>₹{{ number_format($loan_account->bank_sanction_amount + $bank_interest, 2) }}
                                    </td>

                                </tr>
                            </tbody>
                        </table>


                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                NBFC
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">

                    <div class="row brand-white-bg">


                        <table class="table table-stripped table-bordered">
                            <thead>
                                <tr>
                                    <th>Inatallment No</th>
                                    <th>Loan Date</th>
                                    <th>Outstanding Principle</th>
                                    <th>Principle Received</th>
                                    <th>Total Interest</th>
                                    <th>Interest Received</th>
                                    <th>MFL Sprade</th>
                                    <th>Repayment Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>{{ \Carbon\Carbon::parse($loan_account->nbfc_loan_date)->format('d M, Y') }}</td>
                                    <td>₹{{ number_format($loan_account->nbfc_sanction_amount) }}</td>
                                    <td>₹{{ number_format($loan_account->nbfc_sanction_amount) }}</td>
                                    <td>
                                        ₹{{ number_format($nbfc_interest, 2) }}
                                    </td>
                                    <td>
                                        ₹{{ number_format($nbfc_interest, 2) }}
                                    </td>
                                    <td>
                                        @php
                                            $mfl_sprade = $total_interest - $bank_interest - $nbfc_interest;
                                        @endphp
                                        ₹{{ number_format($mfl_sprade, 2) }}
                                    </td>
                                    <td>₹{{ number_format($loan_account->nbfc_sanction_amount + $nbfc_interest + $mfl_sprade, 2) }}
                                    </td>

                                </tr>
                            </tbody>
                        </table>


                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Total
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">

                    <div class="row brand-white-bg">


                        <table class="table table-stripped table-bordered">
                            <thead>
                                <tr>
                                    <th>Inatallment No</th>
                                    <th>Outstanding Principle</th>
                                    <th>Principle Received</th>
                                    <th>Total Interest</th>
                                    <th>Interest Received</th>
                                    <th>Repayment Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>₹{{ number_format($loan_account->sanction_limit, 2) }}</td>
                                    <td>₹{{ number_format($loan_account->sanction_limit, 2) }}</td>
                                    <td>
                                        ₹{{ number_format($total_interest, 2) }}
                                    </td>
                                    <td>
                                        ₹{{ number_format($total_interest, 2) }}
                                    </td>
                                    <td>₹{{ number_format($loan_account->sanction_limit + $total_interest, 2) }}
                                    </td>

                                </tr>
                            </tbody>
                        </table>


                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
