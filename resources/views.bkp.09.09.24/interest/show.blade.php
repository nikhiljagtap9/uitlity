@extends('layout.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Interest History
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">

                    <div class="row brand-white-bg">

                        @foreach ($monthly_interest as $interest)
                            <table class="table table-stripped">
                                <thead>
                                    <tr>
                                        <th colspan="3">
                                            {{ \Carbon\Carbon::parse($interest->interest_date)->format('M-Y') }}
                                        </th>
                                        <th colspan="">{{ number_format($interest->total_interest, 2) }}</th>
                                        <th colspan="">{{ number_format($interest->bank_interest, 2) }}</th>
                                        <th colspan="">{{ number_format($interest->nbfc_interest, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Date</th>
                                        <th>Bank ROI</th>
                                        <th>NBFC ROI</th>
                                        <th>Total Interest</th>
                                        <th>Bank Interest</th>
                                        <th>NBFC Interest</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>

                                </tbody>
                            </table>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
