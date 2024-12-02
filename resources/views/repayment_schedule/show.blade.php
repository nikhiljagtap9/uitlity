@extends('layout.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Application Details
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-5 col-sm-5 col-md-5">
                    <table class="table table-responsive">
                        <tr>
                            <td>Application Number</td>
                            <td>{{ str_replace('LOAN', '', $loan_account->lapp_id) }}</td>
                        </tr>
                        <tr>
                            <td>Sanction Limit</td>
                            <td>₹{{ number_format($loan_account->sanction_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Application Date</td>
                            <td>{{ \Carbon\Carbon::parse($loan_account->LOAN_BOOKING_DATE)->format('d M, Y') }}</td>
                        </tr>

                    </table>
                </div>
                <div class="col-4 col-sm-4 col-md-4">
                    <table class="table table-responsive">
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
                            <td>ROI</td>
                            <td>{{ $setting->nbfc_interest }}%</td>
                        </tr>
                        <tr>
                            <td>Tenure</td>
                            <td>{{ $loan_account->LOAN_TENURE }} Months</td>
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
                                    <th>Due Date</th>
                                    <th>Principle Repayment</th>
                                    <th>Principle Received</th>
                                    <th>Interest</th>
                                    <th>Repayment Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>₹{{ number_format($loan_account->sanction_amount, 2) }}</td>
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
