@extends('layout.app')
@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Batch Details
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">
                    <div class="brand-white-bg">
                        @if (session('success'))
                            <div class="alert alert-success">
                                <p> {{ session('success') }}</p>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-error">
                                <p> {{ session('error') }}</p>
                            </div>
                        @endif
                        @if ($batch['status'] == 'Pending')
                            <div class="alert alert-warning">
                                <p> Batch Pending for CBS Process</p>
                            </div>
                        @endif

                    </div>

                    <table class="table table-stripped table-bordered">
                        <thead>
                            <tr>
                                <th>Batch No</th>
                                <th>Total Principal</th>
                                <th>Total Interest</th>
                                <th>Bank Principal</th>
                                <th>NBFC Principal</th>
                                <th>Bank Interest</th>
                                <th>NBFC Interest</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $batch->uuid }}</td>
                                <td>₹{{ number_format($batch->total_principal, 2) }}</td>
                                <td>₹{{ number_format($batch->total_interest, 2) }}</td>
                                <td>₹{{ number_format($batch->total_bank_principal, 2) }}</td>
                                <td>₹{{ number_format($batch->total_nbfc_principal, 2) }}
                                <td>₹{{ number_format($batch->total_bank_interest, 2) }}
                                <td>₹{{ number_format($batch->total_nbfc_interest, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-stripped table-bordered">
                        <thead>
                            <tr>
                                <th>Loan Account No</th>
                                <th>Principal</th>
                                <th>Interest</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $result)
                                <tr>
                                    <td>{{ $result->loan_account_number }}</td>
                                    <td>{{ $result->principal }}</td>
                                    <td>{{ $result->interest }}</td>
                                    <td>{{ $result->principal + $result->interest }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @if ($batch['status'] == 'Pending')
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    Process to CBS
                </h3>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-3 col-sm-3 col-md-3">
                        <form action="{{ route('cbsapi.store') }}" class="form-group" method="POST">
                            @csrf
                            <input type="hidden" name="batch_id" value="{{ $batch->uuid }}">
                            <input type="hidden" name="cbs_api" value="collection">
                            <button type="submit" class="btn btn-primary" name="submit">Credit To Loan/NBFC
                                Account</button>
                        </form>
                    </div>
                    <div class="col-3 col-sm-3 col-md-3">

                    </div>
                    <div class="col-3 col-sm-3 col-md-3" style="display: none;">
                        <form action="{{ route('cbsapi.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="batch_id" value="{{ $batch->uuid }}">
                            <input type="hidden" name="cbs_api" value="escrow">
                            <button type="submit" class="btn btn-primary" name="submit">Credit To Escrow Account</button>
                        </form>
                    </div>
                    <div class="col-3 col-sm-3 col-md-3"></div>
                </div>
            </div>
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Collection Lists
            </h3>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12">
                <table class="table table-stripped table-bordered">
                    <thead>
                        <tr>
                            <th>MFL No</th>
                            <th>Principal</th>
                            <th>Interest</th>
                            <th>Month</th>
                            <th>Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($collections as $collection)
                            <tr>
                                <td>{{ $collection->REQ_NUMBER }}</td>
                                <td>{{ $collection->PRINCIPAL_AMT }}</td>
                                <td>{{ $collection->INTEREST_AMT }}</td>
                                <td>{{ $collection->MONTH }}</td>
                                <td>{{ $collection->YEAR }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $collections->links() }}

            </div>
        </div>
    </div>
@endsection
