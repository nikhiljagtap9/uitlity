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
                                    <th>Sanction Limit</th>
                                    <th>Bank Sanction Amt(₹)</th>
                                    <th>NBFC Sanction Amt(₹)</th>
                                    <th>Balance(₹)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

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
                ajax: "{{ route('loan_account.index') }}?loan_id={{ $loan_id }}&mfl_ref_no={{ $mfl_ref_no }}",
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
