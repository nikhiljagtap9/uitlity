@extends('layout.app')
@section('head')
    <!-- DataTable -->
    <link rel="stylesheet" href="{{ url('vendors/dataTable/datatables.min.css') }}" type="text/css">

    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Closed Accounts
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">

                    <div class="row brand-white-bg">


                        <table id="example3" class="table table-stripped">
                            <thead>
                                <tr>
                                    <th>Account No</th>
                                    <th>MFL REF No</th>
                                    <th>Sanction Limit</th>
                                    <th>Bank Sanction Amt</th>
                                    <th>NBFC Sanction Amt</th>
                                    <th>Due Date</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loan_accounts as $acct)
                                    <tr>
                                        <td><a target="_blank"
                                                href="{{ route('loan_entries.show', [$acct->loan_id]) }}">{{ $acct->loan_id }}</a>
                                        </td>
                                        <td>{{ $acct->mfl_ref_no }}</td>
                                        <td>{{ $acct->sanction_limit }}</td>
                                        <td>{{ $acct->bank_sanction_amount }}</td>
                                        <td>{{ $acct->nbfc_sanction_amount }}</td>
                                        <td>{{ $acct->loan_tenure }}</td>
                                        <td>{{ number_format($acct->getPrevTotalBalance(), 2) }}
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    Menu
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" target="_blank"
                                                        href="{{ route('loan_entries.list', [$acct->loan_id]) }}">Total
                                                        Ledger</a>
                                                    <a class="dropdown-item" target="_blank"
                                                        href="{{ route('loan_entries.list', [$acct->loan_id, 'bank']) }}">Bank
                                                        Ledger</a>
                                                    <a class="dropdown-item" target="_blank"
                                                        href="{{ route('loan_entries.list', [$acct->loan_id, 'nbfc']) }}">NBFC
                                                        Ledger</a>
                                                    <a class="dropdown-item" target="_blank"
                                                        href="{{ route('repayment_schedule.show', [$acct->loan_id]) }}">Replayment
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
    <!-- DataTable -->
    <script src="{{ url('vendors/dataTable/datatables.min.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.22/sorting/date-de.js"></script>
    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
    <script>
        //
        // Pipelining function for DataTables. To be used to the `ajax` option of DataTables
        //
        $.fn.dataTable.pipeline = function(opts) {
            // Configuration options
            var conf = $.extend({
                pages: 5, // number of pages to cache
                url: '', // script url
                data: null, // function or object with parameters to send to the server
                // matching how `ajax.data` works in DataTables
                method: 'GET' // Ajax HTTP method
            }, opts);

            // Private variables for storing the cache
            var cacheLower = -1;
            var cacheUpper = null;
            var cacheLastRequest = null;
            var cacheLastJson = null;

            return function(request, drawCallback, settings) {
                var ajax = false;
                var requestStart = request.start;
                var drawStart = request.start;
                var requestLength = request.length;
                var requestEnd = requestStart + requestLength;

                if (settings.clearCache) {
                    // API requested that the cache be cleared
                    ajax = true;
                    settings.clearCache = false;
                } else if (cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) {
                    // outside cached data - need to make a request
                    ajax = true;
                } else if (JSON.stringify(request.order) !== JSON.stringify(cacheLastRequest.order) ||
                    JSON.stringify(request.columns) !== JSON.stringify(cacheLastRequest.columns) ||
                    JSON.stringify(request.search) !== JSON.stringify(cacheLastRequest.search)
                ) {
                    // properties changed (ordering, columns, searching)
                    ajax = true;
                }

                // Store the request for checking next time around
                cacheLastRequest = $.extend(true, {}, request);

                if (ajax) {
                    // Need data from the server
                    if (requestStart < cacheLower) {
                        requestStart = requestStart - (requestLength * (conf.pages - 1));

                        if (requestStart < 0) {
                            requestStart = 0;
                        }
                    }

                    cacheLower = requestStart;
                    cacheUpper = requestStart + (requestLength * conf.pages);

                    request.start = requestStart;
                    request.length = requestLength * conf.pages;

                    // Provide the same `data` options as DataTables.
                    if (typeof conf.data === 'function') {
                        // As a function it is executed with the data object as an arg
                        // for manipulation. If an object is returned, it is used as the
                        // data object to submit
                        var d = conf.data(request);
                        if (d) {
                            $.extend(request, d);
                        }
                    } else if ($.isPlainObject(conf.data)) {
                        // As an object, the data given extends the default
                        $.extend(request, conf.data);
                    }

                    return $.ajax({
                        "type": conf.method,
                        "url": conf.url,
                        "data": request,
                        "dataType": "json",
                        "cache": false,
                        "success": function(json) {
                            cacheLastJson = $.extend(true, {}, json);

                            if (cacheLower != drawStart) {
                                json.data.splice(0, drawStart - cacheLower);
                            }
                            if (requestLength >= -1) {
                                json.data.splice(requestLength, json.data.length);
                            }

                            drawCallback(json);
                        }
                    });
                } else {
                    json = $.extend(true, {}, cacheLastJson);
                    json.draw = request.draw; // Update the echo for each response
                    json.data.splice(0, requestStart - cacheLower);
                    json.data.splice(requestLength, json.data.length);

                    drawCallback(json);
                }
            }
        };

        // Register an API method that will empty the pipelined data, forcing an Ajax
        // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
        $.fn.dataTable.Api.register('clearPipeline()', function() {
            return this.iterator('table', function(settings) {
                settings.clearCache = true;
            });
        });


        $(document).ready(function() {

            var table = $('#example3').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('closed') }}",
                "order": [
                    [0, "desc"]
                ],
                columns: [{
                        data: 'loan_id',
                        name: 'loan_id'
                    },
                    {
                        data: 'mfl_ref_no',
                        name: 'mfl_ref_no'
                    },
                    {
                        data: 'sanction_limit',
                        name: 'sanction_limit'
                    },
                    {
                        data: 'bank_sanction_amount',
                        name: 'bank_sanction_amount'
                    },
                    {
                        data: 'nbfc_sanction_amount',
                        name: 'nbfc_sanction_amount'
                    },
                    {
                        data: 'loan_tenure',
                        name: 'loan_tenure'
                    },
                    {
                        data: 'total_balance',
                        name: 'total_balance'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endsection
