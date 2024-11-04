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
                        {{--<tr>
                            <td>UCIC</td>
                            <td>{{ isset($loan_account->ucic) ? $loan_account->ucic : '-' }}</td>
                        </tr>--}}
                    </table>
                </div>
                <div class="col-6 col-sm-6 col-md-">
                    <table class="table table-responsive">
                        <tr>
                            <td>Email</td>
                            <td>{{ isset($loan_account->email) ? $loan_account->email : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Mobile Number</td>
                            <td>{{ isset($loan_account->mobile_number) ? $loan_account->mobile_number : '-' }}</td>
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
                            <td>{{ isset($loan_account->pan_number) ? $loan_account->pan_number : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Credit Score</td>
                            <td>{{ $loan_account->credit_score }}</td>
                        </tr>
                        {{--<tr>
                            <td>Address Proof Type</td>
                            <td>{{ isset($loan_account->address_proof_type) ? $loan_account->address_proof_type : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Address Proof Number</td>
                            <td>****{{ isset($loan_account->address_proof_number) ? substr($loan_account->address_proof_number, -4) : '-' }}
                            </td>
                        </tr>--}}
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Loan Account Details
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-5 col-sm-5 col-md-5">
                    <table class="table table-responsive">
                        <tr>
                            <td>Account Number</td>
                            <td>{{ str_replace('LOAN', '', $loan_account->loan_account_number) }}</td>
                        </tr>
                        <tr>
                            <td>MFL Ref Number</td>
                            <td>{{ $loan_account->mfl_ref_no }}</td>
                        </tr>

                        <tr>
                            <td>Bank Loan Date</td>
                            <td>{{ \Carbon\Carbon::parse($loan_account->bank_loan_date)->format('d-M-Y') }}</td>
                        </tr>
                        <tr>
                            <td>NBFC Loan Date</td>
                            <td>{{ \Carbon\Carbon::parse($loan_account->nbfc_loan_date)->format('d-M-Y') }}</td>
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
                            <td>{{ $closureDate->format('d-M-Y') }}</td>
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
                <div class="col-5 col-sm-5 col-md-5">
                    <table class="table table-responsive">
                        <tr>
                            <td>Total Weight</td>
                            <td>{{ $loan_account->Total_Weight }}</td>
                        </tr>

                        <tr>
                            <td>Name of Valuer</td>
                            <td>{{ $loan_account->Name_Valuer }}</td>
                        </tr>
                        <tr>
                            <td>Role Valuer</td>
                            <td>{{ $loan_account->Role_Valuer }}</td>
                        </tr>
                        <tr>
                            <td>Gross Weight</td>
                            <td>{{ $loan_account->Gross_weight }}</td>
                        </tr>
                        <tr>
                            <td>Total Weight Valuer</td>
                            <td>{{ $loan_account->Total_Weight_Valuer }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-4 col-sm-4 col-md-4">
                    <table class="table table-responsive">

                        <tr>
                            <td>Gold Value</td>
                            <td>{{ $loan_account->Gold_Value }}</td>
                        </tr>
                        <tr>
                            <td>Net Weight</td>
                            <td>{{ $loan_account->Net_Weight}}</td>
                        </tr>
                        <tr>
                            <td>Gold Rate</td>
                            <td>{{ $loan_account->Gold_Rate }}</td>
                        </tr>
                        <tr>
                            <td>Market Rate</td>
                            <td>{{ $loan_account->Market_Rate }}
                            </td>

                        <tr>
                            <td>Total Value</td>
                            <td>{{ $loan_account->Total_Value }}</td>
                        </tr>

                    </table>
                </div>
                <div class="col-3 col-sm-3 col-md-3">
                    <table class="table table-responsive">

                        <tr>
                            <td>Disbursement Date</td>
                            <td>{{ \Carbon\Carbon::parse($loan_account->Date_Disbursement)->format('d-M-Y') }}</td>
                        </tr>

                        <tr>
                            <td>Maturity Date</td>
                            <td>{{ \Carbon\Carbon::parse($loan_account->Maturity_Date)->format('d-M-Y') }}</td>
                        </tr>
                        <tr>
                            <td>Gold_Purity</td>
                            <td>{{ $loan_account->Gold_Purity }}</td>
                        </tr>
                        <tr>
                            <td>Business Type</td>
                            <td>{{ $loan_account->Business_Type }}</td>
                        </tr>
                        <tr>
                            <td>Cersai Date</td>
                            <td>{{ \Carbon\Carbon::parse($loan_account->cersai_date)->format('d-M-Y') }}</td>
                        </tr>


                    </table>
                </div>
            </div>
        </div>
    </div>

    {{--

    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Business Details
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-5 col-sm-5 col-md-5">
                    <table class="table table-responsive">
                        <tr>
                            <td>Customer Id</td>
                            <td>{{ $loan_account->loan_id }}</td>
                        </tr>

                        <tr>
                            <td>Title</td>
                            <td>{{ $loan_account->title }}</td>
                        </tr>
                        <tr>
                            <td>Business Name</td>
                            <td>{{ $loan_account->business_name }}</td>
                        </tr>
                        <tr>
                            <td>Business GST Number</td>
                            <td>{{ $loan_account->business_gst_number }}</td>
                        </tr>
                        <tr>
                            <td>Udyog Aadhar Number</td>
                            <td>{{ $loan_account->udyog_uaadhaar_number }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-4 col-sm-4 col-md-4">
                    <table class="table table-responsive">

                        <tr>
                            <td>Business Address 1</td>
                            <td>{{ $loan_account->business_addr_line1 }}</td>
                        </tr>
                        <tr>
                            <td>Business Address 2</td>
                            <td>{{ $loan_account->business_addr_line2 }}</td>
                        </tr>
                        <tr>
                            <td>Business Zipcode</td>
                            <td>{{ $loan_account->business_zipcode }}</td>
                        </tr>
                        <tr>
                            <td>Business Start Date</td>
                            <td>{{ isset($loan_account->date_of_birth) ? \Carbon\Carbon::parse($loan_account->date_of_birth)->format('d-M-Y') : '-' }}
                            </td>
                        </tr>business_start_date
                        <tr>
                            <td>Credit Score</td>
                            <td>{{ $loan_account->credit_score }}</td>
                        </tr>

                    </table>
                </div>
                <div class="col-3 col-sm-3 col-md-3">
                    <table class="table table-responsive">
                        <tr>
                            <td>Loan City</td>
                            <td>{{ $loan_account->business_city }}</td>
                        </tr>
                        <tr>
                            <td>Loan City State</td>
                            <td>{{ $loan_account->loan_city_state }}</td>
                        </tr>
                        <tr>
                            <td>Loan City State Code</td>
                            <td>{{ $loan_account->loan_city_state_code }}</td>
                        </tr>

                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
--}}
@endsection

