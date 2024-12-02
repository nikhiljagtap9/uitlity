@extends('layout.app')
@section('head')
@endsection
@section('content')
    <style>
        * {
            box-sizing: border-box;
        }

        .fade {
            opacity: 1 !important;
        }

        .modal.fade .modal-dialog {
            transform: translate(0, 10%);
        }

        body {
            background: #e7e7e7;
        }

        .tab {
            width: 100%;
            overflow: hidden;
        }

        .tab button {
            background-color: hsl(210, 17%, 93%);
            border: none;
            outline: none;
            padding: 10px 16px;
            font-weight: bold;
            transition: 0.3s;
        }

        .tab button:hover {
            background-color: #4491d0;
        }

        .tab button.active {
            background-color: #90caf8;
            /* Primary blue for active tab */
            color: rgb(4, 1, 1);
        }

        .tabcontent {
            background: #fff;
            width: 100%;
            display: none;
            padding: 6px 12px;
            font-family: arial;
            line-height: 21px;
        }
    </style>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Disbursement Batches
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">
                    <div class="row brand-white-bg">
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
                    </div>

                    <table id="example3" class="table table-stripped table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th>Batch No</th>
                                <th>Date</th>
                                <th>Sanction Amount(₹)</th>
                                <th>Bank Sanction Amount(₹)</th>
                                <th>NBFC Sanction Amount(₹)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($batches as $batch)
                                <tr>
                                    <td>
                                        <a href="{{ route('disbursebatch.show', [$batch]) }}">
                                            {{ $batch->uuid }}</a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($batch->created_at)->format('d-m-Y') }}</td>
                                    <td align="right">{{ number_format($batch->total_sanction_amount, 2) }}</td>
                                    <td align="right">
                                        {{ number_format($batch->bank_sanction_amount, 2) }}
                                    </td>
                                    <td align="right">{{ number_format($batch->nbfc_sanction_amount, 2) }}</td>
                                    <td>{{ $batch->status }}</td>
                                    <td>
                                        <a href="{{ route('disbursebatch.show', [$batch]) }}"
                                            class="btn btn-primary">View</a>
                                        @if ($batch->status != 'Disbursed')
                                            <form id="deleteForm" action="{{ route('disbursebatch.destroy', [$batch]) }}"
                                                method="POST" class="form-group">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="DELETE">
                                            </form>
                                            <button class="btn btn-danger " data-toggle="modal"
                                                data-target="#confirmationModal" data-backdrop = "false">Delete</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $batches->links() }}
                </div>
            </div>

        </div>
    </div>



    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <div class="alert text-danger" role="alert">
                        <strong>Are you sure you want to perform this action?</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmit">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to handle form submission when "Confirm" is clicked
        document.getElementById('confirmSubmit').addEventListener('click', function() {
            // Submit the form
            document.getElementById('deleteForm').submit();
        });
    </script>
@endsection
@section('script')
@endsection
