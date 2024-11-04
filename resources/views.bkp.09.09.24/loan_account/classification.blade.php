@extends('layout.app')
@section('head')
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                {{ $classification }} Accounts
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">

                    <div class="row">


                        <table id="example3" class="table table-stripped">
                            <thead>
                                <tr>
                                    <th>Account No</th>
                                    <th>MFL REF No</th>
                                    <th>Sanction Limit</th>
                                    <th>Tenure</th>
                                    <th>Closure Date</th>
                                    <th>DPD</th>
                                    <th>o/s Balance</th>
                                    <!-- <th>Principal</th> -->
                                    <th>o/s Interest</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($loan_accounts as $acct)
                                    @php
                                        $tenure = $acct->loan_tenure;
                                        $bank_loan_date = $acct->bank_loan_date;
                                        $start = \Carbon\Carbon::parse($bank_loan_date);

                                        $closureDate = $start->addMonths($tenure);
                                        $currentDate = \Carbon\Carbon::now();
                                        $daysDifference = $currentDate->diffInDays($closureDate, true);
                                    @endphp
                                    <tr>
                                        <td><a target="_blank"
                                                href="{{ route('loan_entries.show', [$acct->loan_id]) }}">{{ $acct->loan_id }}</a>
                                        </td>
                                        <td>{{ $acct->mfl_ref_no }}</td>
                                        <td>{{ $acct->sanction_limit }}</td>
                                        <td>{{ $acct->loan_tenure }}</td>
                                        <td>{{ $closureDate->format('d-m-Y') }}</td>
                                        <td>{{ $daysDifference }}</td>
                                        <td>{{ number_format($acct->getPrevPrincipalBalance(), 2) }}
                                        </td>
                                        @if ($classification == 'NPA')
                                            <td>{{ number_format($acct->getCurrentInterestBalance()['total_interest'], 2) }}

                                            </td>
                                        @else
                                            <td>{{ number_format($acct->getPrevInterestBalance(), 2) }}

                                            </td>
                                        @endif

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
@endsection
