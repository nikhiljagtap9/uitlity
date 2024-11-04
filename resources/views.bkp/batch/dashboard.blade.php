@extends('layout.app')
@section('head')
    <script src="{{ url('chart.js/dist/chart.umd.js') }}"></script>

    <style>
        .card-header {
            padding: 10px 0 !important;
        }

        .card {
            box-shadow: 0px 3px 4px 0px rgba(0, 0, 0, 0.2) !important;
        }

        .card-body {
            padding: 2rem 0;
        }

        .card-title {
            margin-bottom: 10px;
            float: none;
        }
    </style>
@endsection
@section('content')
    <div class="row">
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> Total Accounts
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($count['allcount']) }}
                        <a href="{{ route('exportCSV') }}" class="card-text"><i class="fa fa-download"></i></a>
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> Total Active Accounts
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($count['accounts']) }}
                        <a href="{{ route('exportCSV', ['loan_status' => 'active']) }}" class="card-text"><i
                                class="fa fa-download"></i></a>
                    </h5>
                    <p class="card-text"></p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> Total Closed Accounts
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($count['accounts_closed']) }}<br>
                        <a href="{{ route('exportCSV', ['loan_status' => 'closed']) }}" class="card-text"><i
                                class="fa fa-download"></i> Download CSV</a>
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> Total Colending Amount
                </div>
                <div class="card-body">
                    <h5 class="card-title">₹{{ number_format($count['total_disbursement'], 2) }}</h5>
                    <p class="card-text">LTV {{ $count['total_ltv'] }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="display: none">


        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> NBFC Colending Amount
                </div>
                <div class="card-body">
                    <h5 class="card-title">₹{{ number_format($count['nbfc_disbursement'], 2) }}</h5>
                    <p class="card-text">LTV {{ $count['nbfc_ltv'] }} </p>
                </div>
            </div>
        </div>


    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> Bank Colending Amount
                </div>
                <div class="card-body">
                    <h5 class="card-title">₹{{ number_format($count['bank_disbursement'], 2) }}</h5>
                    <p class="card-text">LTV {{ $count['bank_ltv'] }} </p>

                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> Bank Total Received
                </div>
                <div class="card-body">
                    <h5 class="card-title">
                        ₹{{ number_format($count['total_bank_principal'] + $count['total_bank_interest'], 2) }}</h5>

                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> Bank Principal Received
                </div>
                <div class="card-body">
                    <h5 class="card-title">₹{{ number_format($count['total_bank_principal'], 2) }}</h5>

                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> Bank Interest Received
                </div>
                <div class="card-body">
                    <h5 class="card-title">₹{{ number_format($count['total_bank_interest'], 2) }}</h5>
                    <p class="card-text"></p>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> SMA0
                </div>
                <div class="card-body">
                    <h5 class="card-title">
                        {{ $count['sma0'] }}</h5>

                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> SMA1
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $count['sma1'] }}</h5>

                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> SMA2
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $count['sma2'] }}</h5>
                    <p class="card-text"></p>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-layer-group"></i> NPA
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $count['npa'] }}</h5>
                </div>
            </div>
        </div>
    </div>



    <div class="row" style="display: none;">
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">₹{{ number_format($count['total_credit'], 2) }}</h5>
                    <p class="card-text">Total Credit</p>

                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">₹{{ number_format($count['total_debit'], 2) }}</h5>
                    <p class="card-text">Total Debit</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">No. of Accounts Accquired (Last 15-Days)</div>
                <div class="card-body">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">Disbursement Amount (Last 15-Days)</div>
                <div class="card-body">
                    <canvas id="barChart2"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var ctx = document.getElementById('barChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($data->map(fn($data) => $data->bank_loan_date)),
                datasets: [{
                    label: 'Number of Accounts',
                    data: @json($data->map(fn($data) => $data->aggregate)),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });


        var ctx2 = document.getElementById('barChart2').getContext('2d');
        var myChart2 = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: @json($data2->map(fn($data2) => $data2->bank_loan_date)),
                datasets: [{
                    label: 'Disbursement',
                    data: @json($data2->map(fn($data2) => $data2->aggregate2)),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
