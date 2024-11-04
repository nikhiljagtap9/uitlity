@extends('layout.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Update Interest
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">
                    <form action="{{ route('setting.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="bank_interest">Bank Interest</label>
                            <input type="text" name="bank_interest" id="bank_interest" class="form-control"
                                value="{{ $setting->bank_interest }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nbfc_interest">NBFC Interest</label>
                            <input type="text" name="nbfc_interest" id="nbfc_interest" class="form-control"
                                value="{{ $setting->nbfc_interest }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nbfc_interest">Gold Rate</label>
                            <input type="text" name="benchmark_rate" id="benchmark_rate" class="form-control"
                                value="{{ $setting->benchmark_rate }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nbfc_interest">CBS Loan Account Number</label>
                            <input type="text" name="loan_account_number" id="loan_account_number" class="form-control"
                                value="{{ $setting->loan_account_number }}">
                        </div>
                        <div class="form-group">
                            <input type="submit" name="Save" id="save" class="btn btn-primary" value="Save">
                        </div>
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
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Update History
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Bank Interest</th>
                                <th>Nbfc Interest</th>
                                <th>Benchmark Rate</th>
                                <th>CBS Loan Account Number</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($settings as $st)
                                <tr>
                                    <td>{{ $st->bank_interest }}</td>
                                    <td>{{ $st->nbfc_interest }}</td>
                                    <td>{{ $st->benchmark_rate }}</td>
                                    <td>{{ $st->loan_account_number }}</td>
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
