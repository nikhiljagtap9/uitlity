@extends('layout.app')
@section('head')
@endsection
@section('content')
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

                    <table id="example3" class="table table-stripped table-bordered">
                        <thead>
                            <tr>
                                <th>Batch No</th>
                                <th>Date</th>
                                <th>Total Principal(₹)</th>
                                <th>Total Interest(₹)</th>
                                <th>Total Bank Interest(₹)</th>
                                <th>Total NBFC Interest(₹)</th>
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
                                    <td align="right">{{ number_format($batch->total_loan_amount, 2) }}</td>
                                    <td align="right">{{ number_format($batch->total_sanction_amount, 2) }}</td>
                                    <td align="right">{{ number_format($batch->nbfc_sanction_amount, 2) }}</td>
                                    <td align="right">
                                        {{ number_format($batch->bank_sanction_amount, 2) }}
                                    </td>
                                    <td>{{ $batch->status }}</td>
                                    <td>
                                        <a href="{{ route('disbursebatch.show', [$batch]) }}"
                                            class="btn btn-primary">View</a>
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
@endsection

@section('script')
@endsection
