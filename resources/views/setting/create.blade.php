@extends('layout.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Update Settings
            </h3>
        </div>
        <div class="card-body">
	    <div class="row">
		@if ($errors->any())
                            <div>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                <p> {{ session('success') }}</p>
                            </div>
                        @endif

		<form action="{{ route('setting.store') }}" method="POST">
                        @csrf

		<div class="col-6 col-sm-6 col-md-6">
			 <div class="form-group">
                            <label class="form-label" for="bank_roi">Bank Interest</label>
                            <input type="text" name="bank_roi" id="bank_roi" class="form-control"
                                value="{{ $setting->bank_roi }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="nbfc_interest">NBFC Interest</label>
                            <input type="text" name="nbfc_interest" id="nbfc_interest" class="form-control"
                                value="{{ $setting->nbfc_interest }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="benchmark_rate">Gold Rate</label>
                            <input type="text" name="benchmark_rate" id="benchmark_rate" class="form-control"
                                value="{{ $setting->benchmark_rate }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="loan_account_number_agri">Debit Loan Account(AGRI)</label>
                            <input type="text" name="loan_account_number_agri" id="loan_account_number_agri" class="form-control"
                                value="{{ $setting->loan_account_number_agri }}">
                        </div>
			<div class="form-group">
                            <label class="form-label" for="loan_account_number_msme">Debit Loan Account(MSME)</label>
                            <input type="text" name="loan_account_number_msme" id="loan_account_number_msme" class="form-control"
                                value="{{ $setting->loan_account_number_msme }}">
			</div>
		</div>
		<div class="col-6 col-sm-6 col-md-6">
                
			<div class="form-group">
                            <label class="form-label" for="to_loan_account_number_agri">Credit Loan Account(AGRI)</label>
                            <input type="text" name="to_loan_account_number_agri" id="to_loan_account_number_agri" class="form-control"
                                value="{{ $setting->to_loan_account_number_agri }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="to_loan_account_number_msme">Credit Loan Account(MSME)</label>
                            <input type="text" name="to_loan_account_number_msme" id="to_loan_account_number_msme" class="form-control"
                                value="{{ $setting->to_loan_account_number_msme }}">
			</div>

			 <div class="form-group">
                            <label class="form-label" for="service_fee">Service Fee</label>
                            <input type="text" name="service_fee" id="service_fee" class="form-control"
                                value="{{ $setting->service_fee }}">
                        </div>


			<div class="form-group">
                            <label class="form-label" for="gst">GST(in %)</label>
                            <input type="text" name="gst" id="gst" class="form-control"
                                value="{{ $setting->gst }}">
                        </div>
                        
			 <div class="form-group">
                            <input type="checkbox" name="pan_checkbox" id="pan_checkbox">
                            <label for="pan_checkbox">PAN</label>
                        </div>

                        <div class="form-group">
                            <input type="checkbox" name="ckyc_checkbox" id="ckyc_checkbox">
                            <label for="ckyc_checkbox">CKYC</label>
                        </div>

                        <div class="form-group">
                            <input type="checkbox" name="udyam_checkbox" id="udyam_checkbox">
                            <label for="udyam_checkbox">UDYAM</label>
                        </div>


                        <div class="form-group">
                            <input type="submit" name="Save" id="save" class="btn btn-primary" value="Save">
                        </div>
                   </div>
		</form>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Settings History
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Effective Interest</th>
				<th>Nbfc Interest</th>
                                <th>Benchmark Rate</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($settings as $st)
                                <tr>
                                    <td>{{ $st->bank_interest }}</td>
				    <td>{{ $st->nbfc_interest }}</td>
                                    <td>{{ $st->benchmark_rate }}</td>
                                    <td>{{ date('d-m-Y', strtotime($st->created_at)) }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
