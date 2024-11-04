@extends('layout.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Personal Details
            </h3>
            <a style="float: right" href="{{ route('downloadZip', [$loan_account->loan_id]) }}"
                class="btn btn-primary">Download KYC Documents</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6 col-sm-6 col-md-">
                    <table class="table table-responsive">
                        <tr>
                            <td>Customer Name</td>
                            <td>{{ isset($loan_account->customer_title) ? $loan_account->customer_title : '' }}
                                {{ isset($loan_account->customer_name) ? $loan_account->customer_name : '' }}</td>
                        </tr>
                        <tr>
                            <td>Address1</td>
                            <td>{{ isset($loan_account->address1) ? $loan_account->address1 : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Address2</td>
                            <td>{{ isset($loan_account->address2) ? $loan_account->address2 : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Postal Code</td>
                            <td>{{ isset($loan_account->postal_code) ? $loan_account->postal_code : '-' }}</td>
                        </tr>
                        <tr>
                            <td>State Code</td>
                            <td>{{ isset($loan_account->state_code) ? $loan_account->state_code : '-' }}</td>
                        </tr>
                        <tr>
                            <td>City Code</td>
                            <td>{{ isset($loan_account->city_code) ? $loan_account->city_code : '-' }}</td>
                        </tr>
                        <tr>
                            <td>CKYC</td>
                            <td>{{ isset($loan_account->ckyc_no) ? $loan_account->ckyc_no : '-' }}</td>
                        </tr>
                        <tr>
                            <td>UCIC</td>
                            <td>{{ isset($loan_account->ucic) ? $loan_account->ucic : '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-6 col-sm-6 col-md-">
                    <table class="table table-responsive">
                        <tr>
                            <td>Date of Birth</td>
                            <td>{{ isset($loan_account->date_of_birth) ? \Carbon\Carbon::parse($loan_account->date_of_birth)->format('d-M-Y') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ isset($loan_account->email) ? $loan_account->email : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Mobile Number</td>
                            <td>{{ isset($loan_account->mobilenumber) ? $loan_account->mobilenumber : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td>{{ isset($loan_account->gender) ? $loan_account->gender : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Cast/Community</td>
                            <td>{{ isset($loan_account->caste) ? $loan_account->caste : '-' }}/{{ isset($loan_account->community) ? $loan_account->community : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>PAN Number</td>
                            <td>{{ isset($loan_account->permanentaccountnumberpan) ? $loan_account->permanentaccountnumberpan : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Address Proof Type</td>
                            <td>{{ isset($loan_account->address_proof_type) ? $loan_account->address_proof_type : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Address Proof Number</td>
                            <td>****{{ isset($loan_account->address_proof_number) ? substr($loan_account->address_proof_number, -4) : '-' }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
                            <td>MFL Ref Number</td>
                            <td>{{ $loan_account->mfl_ref_no }}</td>
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
                            <td>Sanction Limit</td>
                            <td>₹{{ number_format($loan_account->sanction_limit, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Bank Sanction Amount</td>
                            <td>₹{{ number_format($loan_account->bank_sanction_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>NFBC Sanction Amount</td>
                            <td>₹{{ number_format($loan_account->nbfc_sanction_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Outstanding Total</td>
                            <td>₹{{ number_format($loan_account->getPrevTotalBalance(), 2) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-3 col-sm-3 col-md-3">
                    <table class="table table-responsive">
                        <tr>
                            <td>Tenure</td>
                            <td>{{ $loan_account->loan_tenure }} Months</td>
                        </tr>
                        <tr>
                            <td>Bank ROI</td>
                            <td>{{ $setting->bank_interest }}%</td>
                        </tr>
                        <tr>
                            <td>NBFC ROI</td>
                            <td>{{ $setting->nbfc_interest }}%</td>
                        </tr>
                        <tr>
                            <td>Closure Date</td>
                            <td>{{ $closureDate->format('d M, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Gold Details
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-4 col-sm-4 col-md-4">
                    <table class="table table-responsive">
                        <tr>
                            <td>Gold Carat Value</td>
                            <td>{{ $loan_account->gold_carat_value }}</td>
                        </tr>
                        <tr>
                            <td>Total Current Security</td>
                            <td>₹{{ number_format($loan_account->gold_quantity * ($setting['benchmark_rate'] / 10), 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-4 col-sm-4 col-md-4">
                    <table class="table table-responsive">
                        <tr>
                            <td>Gold Quantity</td>
                            <td>{{ number_format($loan_account->gold_quantity, 2) }} gm</td>
                        </tr>
                        <tr>
                            <td>LTV</td>
                            <td>{{ number_format($loan_account->bank_sanction_amount / ($loan_account->gold_quantity * ($setting['benchmark_rate'] / 10)), 3) * 100 }}%
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-2 col-sm-2 col-md-2">
                    <table class="table table-responsive">
                        <tr>
                            <td>Gold Rate</td>
                            <td>₹{{ number_format($setting['benchmark_rate'] / 10) }}</td>
                        </tr>
                        <tr>
                            <td>Scheme</td>
                            <td>{{ $loan_account->scheme }}</td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection
