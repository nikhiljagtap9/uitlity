@extends('layout.app')
@section('head')
    <link rel="stylesheet" type="text/css" href="{{ env('BASE').('/all-files/plugins/daterangepicker/daterangepicker.css') }}" />

    <style>
        .alert-warning {
            color: #FFF;
            padding: 0 5px;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Search
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="">
                <div class="row">
                    <div class="col-4 col-sm-4 col-md-4">
                        <div class="form-group">
                            <label for="loan_id">Account Number</label>
                            <input type="text" name="loan_id" value="{{ $loan_id }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-1 col-sm-1 col-md-1">
                        -- OR --
                    </div>
                    <div class="col-4 col-sm-4 col-md-4">
                        <div class="form-group">
                            <label for="loan_id">MFL Ref Number</label>
                            <input type="text" name="mfl_ref_no" value="{{ $mfl_ref_no }}" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-12">
                        <input type="submit" class="btn btn-primary" name="submit" value="Search">
                        <a href="{{ route('loan_account.index') }}" class="btn btn-default">Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Disbursed Loans
            </h3>
            <div style="float: right">
                <div id="reportrange"
                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; float:left; margin-right:20px;">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>

                <a href="{{ route('exportCSV', ['start_date' => $start_date, 'end_date' => $end_date]) }}"
                    class="btn btn-primary">Export</a>
                <p id="exportProcess" class="alert alert-warning" style="display: none; margin:0 20px; float:left;">
                    Please
                    wait processing
                    download
                    request...</p>
                <a id="downloadLink" class="btn btn-secondary" style="display: none;margin:0 20px;float:left;">Download</a>
            </div>
            <div style="clear: both"></div>

        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">

                    <div class="">


                        <table id="example3" class="table table-stripped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Account No</th>
                                    <th>MFL REF No</th>
                                    <th>Sanction Limit(₹)</th>
                                    <th>Bank Sanction Amt(₹)</th>
                                    <th>NBFC Sanction Amt(₹)</th>
                                    <th>Balance(₹)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loan_accounts as $account)
                                    <tr>
                                        <td><a
                                                href="{{ route('loan_account.show', [$account->loan_id]) }}">{{ str_replace('LOAN', '', $account->loan_id) }}</a>
                                        </td>
                                        <td>{{ $account->mfl_ref_no }}</td>
                                        <td align="right">{{ number_format($account->sanction_limit, 2) }}</td>
                                        <td align="right">{{ number_format($account->bank_sanction_amount, 2) }}</td>
                                        <td align="right">{{ number_format($account->nbfc_sanction_amount, 2) }}</td>
                                        <td align="right">{{ number_format($account->getPrevTotalBalance(), 2) }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    Menu
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" target="_blank"
                                                        href="{{ route('loan_entries.list', [$account->loan_id]) }}">Total
                                                        Ledger</a>
                                                    <a class="dropdown-item" target="_blank"
                                                        href="{{ route('loan_entries.list', [$account->loan_id, 'bank']) }}">Bank
                                                        Ledger</a>
                                                    <a class="dropdown-item" target="_blank"
                                                        href="{{ route('loan_entries.list', [$account->loan_id, 'nbfc']) }}">NBFC
                                                        Ledger</a>
                                                    <a class="dropdown-item" target="_blank"
                                                        href=" {{ route('repayment_schedule.show', [$account->loan_id]) }}">Replayment
                                                        Schedule</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $loan_accounts->links() }}


                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript" src="{{ env('BASE').('/all-files/plugins/daterangepicker/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ env('BASE').('/all-files/plugins/daterangepicker/daterangepicker.min.js') }}"></script>

    <script>
        var start_date, end_date;
        /*
        document.getElementById('exportButton').addEventListener('click', function() {
            //document.getElementById('exportButton').style.display = 'none';
            document.getElementById('exportProcess').style.display = 'block'
            fetch('/collection/loanaccount/export/?start_date=' + start_date + '&end_date=' + end_date)
                .then(response => response.json())
                .then(data => {
                    if (data.filePath) {
                        const downloadLink = document.getElementById('downloadLink');
                        downloadLink.href = `/collection/download/${data.filePath}`;
                        document.getElementById('exportProcess').style.display = 'none';
                        downloadLink.style.display = 'block';
                    }
                });
        });
        */
        $(function() {
            @if ($start_date)
                var start = moment('{{ $start_date }}');
                var end = moment('{{ $end_date }}');
                start_date = start.format('YYYY-MM-DD');
                end_date = end.format('YYYY-MM-DD');
            @else
                var start = moment().subtract(30, 'days');
                var end = moment().subtract(1, 'days');
                start_date = start.format('YYYY-MM-DD');
                end_date = end.format('YYYY-MM-DD');
            @endif

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);
            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                start_date = picker.startDate.format('YYYY-MM-DD');
                end_date = picker.endDate.format('YYYY-MM-DD');
                window.location.href = '{{ route('loan_account.index') }}?start_date=' + picker.startDate
                    .format('YYYY-MM-DD') + '&end_date=' + picker.endDate.format('YYYY-MM-DD');
            });

        });
    </script>
@endsection
