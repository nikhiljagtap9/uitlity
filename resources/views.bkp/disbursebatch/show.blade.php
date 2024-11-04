@extends('layout.app')
@section('content')
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
                <p> Batch Pending for BRE Process</p>
            </div>
        @endif
        @if ($batch['status'] == 'Approved')
            <div class="alert alert-warning">
                <p> Batch Pending for CBS Process</p>
            </div>
        @endif

    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Disbursement Batch Details
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">

                    <table class="table table-stripped table-bordered">
                        <thead>
                            <tr>
                                <th>Batch No</th>
                                <th>No. of Loans</th>
                                <th>Total Loan Amount</th>
                                <th>Total Sanction Amount</th>
                                <th>Total Nbfc Sanction Amount</th>
                                <th>Total Bank Sanction Amount</th>

                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td>{{ $batch->uuid }}</td>
                                <td>{{ $collection_count }}</td>
                                <td>₹{{ number_format($batch->total_loan_amount, 2) }}</td>
                                <td>₹{{ number_format($batch->total_sanction_amount, 2) }}</td>
                                <td>₹{{ number_format($batch->nbfc_sanction_amount, 2) }}</td>
                                <td>₹{{ number_format($batch->bank_sanction_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                BRE Details
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">
                    <div class="brand-white-bg">
                        <table class="table table-stripped table-bordered">
                            <thead>
                                <tr>
                                    <th>No. of Loans</th>
                                    <th>Total Loan Amount</th>
                                    <th>Total Sanction Amount</th>
                                    <th>Total Nbfc Sanction Amount</th>
                                    <th>Total Bank Sanction Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $result)
                                    <tr>
                                        <td>{{ $result->status_count }}</td>
                                        <td>₹{{ number_format($result->loan_amount, 2) }}</td>
                                        <td>₹{{ number_format($result->sanction_amount, 2) }}</td>
                                        <td>₹{{ number_format($result->nbfc_sanction_amount, 2) }}</td>
                                        <td>₹{{ number_format($result->bank_sanction_amount, 2) }}</td>

                                        <td>{{ $result->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        @if ($batch['status'] == 'Pending' && $pending_count > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        Run BRE
                    </h3>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12">
                            <button class="btn btn-success" id="start-processing">Start Processing</button>
                            <div id="progress">Progress: 0%</div>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="progress" style="width: 100%; height:20px; padding:0;">
                                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endif
        @if ($batch['status'] == 'Approved' && $approved_count>0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        CBS Process
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3 col-sm-3 col-md-3">
                            <form action="{{ route('disbursment') }}" class="form-group" method="POST">
                                @csrf
                                <input type="hidden" name="batch_id" value="{{ $batch->uuid }}">
                                <button type="submit" class="btn btn-primary" name="submit">Disbursed to Escrow
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
                                <button type="submit" class="btn btn-primary" name="submit">Credit To Escrow
                                    Account</button>
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
                    Disbursement Lists
                </h3>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12">
                    <table class="table table-stripped table-bordered">
                        <thead>
                            <tr>
                                <th>MFL No</th>
                                <th>Customer Name</th>
                                <th>Loan Amount</th>
                                <th>Sanction Amount</th>
                                <th>Bank Sanction Amount</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($collections as $collection)
                                <tr>
                                    <td>{{ $collection->mfl_loan_id }}</td>
                                    <td>{{ $collection->CUSTOMER_NAME }}</td>
                                    <td>{{ $collection->loan_amount }}</td>
                                    <td>{{ $collection->sanction_amount }}</td>
                                    <td>{{ $collection->bank_sanction_amount }}</td>
                                    <td>{{ $collection->message }}</td>
                                    <td>{{ $collection->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $collections->links() }}

                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <script>
            $(document).ready(function() {
                $('#start-processing').click(function() {

                    processChunk(0); // Start processing from offset 0
                });

                function processChunk(offset) {
                    $('#start-processing').hide();
                    $.ajax({
                        url: "{{ route('processChunks') }}",
                        method: 'POST',
                        data: {
                            offset: offset,
                            batch_id: '{{ $batch->uuid }}',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // Update the progress UI
                            updateProgress(offset);

                            if (response.moreData) {
                                // Continue processing the next chunk
                                processChunk(response.nextOffset);
                            } else {
                                // All done
                                $('#progress').text(
                                    'Processing complete! Please wait... Redirecting in 10 seconds...');

                                setTimeout(function() {
                                    window.location.href =
                                        "{{ route('disbursebatch.show', [$batch]) }}";
                                }, 10000); // 10,000 milliseconds = 10 seconds

                            }
                        },
                        error: function() {
                            $('#start-processing').show();
                            $('#progress').text('An error occurred during processing.');
                        }
                    });
                }

                function updateProgress(offset) {
                    // Assuming you know the total number of records
                    const totalRecords = {{ $pending_count }};
                    const percentage = Math.min(100, (offset / totalRecords) * 100);
                    $('#progress-bar').css('width', percentage.toFixed(2) + '%').attr('aria-valuenow', percentage
                        .toFixed(2));
                    $('#progress').text('Progress: ' + percentage.toFixed(2) + '%');
                }
            });
        </script>
    @endsection
