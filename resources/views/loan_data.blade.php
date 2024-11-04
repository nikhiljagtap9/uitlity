@extends('layout.app')

@section('head')
    <script src="{{ url('chart.js/dist/chart.umd.js') }}"></script>

    <style>
        .card-header {
            padding: 2px 0 !important;
        }

        .card {
            box-shadow: 0px 3px 4px 0px rgba(0, 0, 0, 0.2) !important;
        }

        .card-body {
            padding: 1rem 0;
        }

        .card-title {
            margin-bottom: 10px;
            float: none;
        }

        table {
            margin-bottom: 0 !important;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
@endsection

@php
function formatIndianCurrency($amount) {
    $amount = floatval($amount);
    $formattedAmount = number_format($amount, 2, '.', '');

    // Split the formatted amount into integer and decimal parts
    list($integerPart, $decimalPart) = explode('.', $formattedAmount);

    $len = strlen($integerPart);    // Format the integer part for Indian currency
    $formattedIntegerPart = '';

    if ($len > 3) {
        $formattedIntegerPart = substr($integerPart, -3); // Get the last 3 digits
        $remainingDigits = substr($integerPart, 0, $len - 3); // Get the remaining digits

        // Place commas after every two digits in the remaining digits
        $formattedIntegerPart = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remainingDigits) . ',' . $formattedIntegerPart;
    } else {
        $formattedIntegerPart = $integerPart;
    }
    // Combine integer part and decimal part
    $formattedAmount = $formattedIntegerPart . '.' . $decimalPart;
    return '₹' . $formattedAmount;
}

function indianFormat($ct) {
    $ct = intval($ct);
    $formattedNumber = '';

    $len = strlen($ct);
    if ($len > 3) {
        $formattedNumber = substr($ct, -3);
        $remainingDigits = substr($ct, 0, $len - 3);

        $formattedNumber = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remainingDigits) . ',' . $formattedNumber;
    } else {
        $formattedNumber = $ct;
    }

    return $formattedNumber;
}
@endphp

@section('content')
 <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Muthoot Gold</h2>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total Accounts</th>
                            <th>Bank Sanction Amount</th>
                            <th>NBFC Sanction Amount</th>
                        </tr>
                    </thead>
                    <tbody>
  {{-- Muthoot Gold Active --}}
                        <tr>
                            <td>Active</td>
                            <td>{{ indianFormat($count['muthoot_gold_active']['muthoot_gold_total_active_acc']) }}</td>
                            <td>{{ formatIndianCurrency($count['muthoot_gold_active']['muthoot_gold_sanction_amount_active'], 2) }}</td>
                            <td>{{ formatIndianCurrency($count['muthoot_gold_active']['muthoot_gold_nbfc_sanction_amount_active'], 2) }}</td>
                        </tr>

                        {{-- Capri Closed --}}
                        <tr>
                            <td>Closed</td>
                            <td>{{ indianFormat($count['muthoot_gold_closed']['muthoot_gold_total_closed_acc']) }}</td>
                            <td>{{ formatIndianCurrency($count['muthoot_gold_closed']['muthoot_gold_bank_sanction_closed'], 2) }}</td>
                            <td>{{ formatIndianCurrency($count['muthoot_gold_closed']['muthoot_gold_nbfc_sanction_closed'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div>

<div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Muthoot MSME</h2>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total Accounts</th>
                            <th>Bank Sanction Amount</th>
                            <th>NBFC Sanction Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Muthoot MSME Active --}}
                        <tr>
                            <td>Active</td>
                            <td>{{ indianFormat($count['muthoot_active']['muthootMSME_total_active_acc']) }}</td>
                            <td>{{ formatIndianCurrency($count['muthoot_active']['muthootMSME_bank_sanction_amount_active'], 2) }}</td>
                            <td>{{ formatIndianCurrency($count['muthoot_active']['muthootMSME_nbfc_sanction_amount_active'], 2) }}</td>
                        </tr>

                        {{-- Muthoot MSME Closed --}}
                        <tr>
                            <td>Closed</td>
                            <td>{{ indianFormat($count['muthoot_closed']['muthootMSME_total_closed_acc']) }}</td>
                            <td>{{ formatIndianCurrency($count['muthoot_closed']['muthootMSME_bank_sanction_amount_closed'], 2) }}</td>
                            <td>{{ formatIndianCurrency($count['muthoot_closed']['muthootMSME_nbfc_sanction_amount_closed'], 2) }}</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

 <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Capri</h2>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total Accounts</th>
                            <th>Bank Sanction Amount</th>
                            <th>NBFC Sanction Amount</th>
                        </tr>
                    </thead>
                    <tbody>

                        {{-- Capri Active --}}
                        <tr>
                            <td>Active</td>
                            <td>{{ indianFormat($count['capri_active']['capri_total_active_acc']) }}</td>
                            <td>{{ formatIndianCurrency($count['capri_active']['capri_bank_sanction_amount_active'], 2) }}</td>
                            <td>{{ formatIndianCurrency($count['capri_active']['capri_nbfc_sanction_amount_active'], 2) }}</td>
                        </tr>

                        {{-- Capri Closed --}}
                        <tr>
                            <td>Closed</td>
                            <td>{{ indianFormat($count['capri_closed']['capri_total_closed_acc']) }}</td>
                            <td>{{ formatIndianCurrency($count['capri_closed']['capri_bank_sanction_amount_closed'], 2) }}</td>
                            <td>{{ formatIndianCurrency($count['capri_closed']['capri_nbfc_sanction_amount_closed'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div>

		 <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">LendingKart</h2>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total Accounts</th>
                            <th>Bank Sanction Amount</th>
                            <th>NBFC Sanction Amount</th>
                        </tr>
                    </thead>
                    <tbody>

                        {{-- Capri Active --}}

                     @foreach($count['loanData'] as $item)
                        <tr>
                            <td>{{ $item->loan_status }}</td>
                            <td>{{ indianFormat($item->total)  }}</td>
                            <td>{{ formatIndianCurrency($item->bank_sanction_amount , 2) }}</td>
                            <td>{{ formatIndianCurrency($item->nbfc_sanction_amount, 2) }}</td>
                        </tr>
                     @endforeach


                    </tbody>
                </table>
            </div>
        </div>
        </div>

  <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">MAS</h2>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total Accounts</th>
                            <th>Bank Sanction Amount</th>
                            <th>NBFC Sanction Amount</th>
                        </tr>
                    </thead>
                    <tbody>

                        {{-- Capri Active --}}

                     @foreach($count['masLoanData'] as $item)
                        <tr>
                            <td>{{ $item->loan_status }}</td>
                            <td>{{ indianFormat($item->total)}}</td>
                            <td>{{ formatIndianCurrency($item->bank_sanction_amount , 2) }}</td>
                            <td>{{ formatIndianCurrency($item->nbfc_sanction_amount, 2) }}</td>
                        </tr>
                     @endforeach


                    </tbody>
                </table>
            </div>
        </div>
        </div>

		 <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Loantap</h2>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total Accounts</th>
                            <th>Bank Sanction Amount</th>
                            <th>NBFC Sanction Amount</th>
                        </tr>
                    </thead>
                    <tbody>

                        {{-- Capri Active --}}

                     @foreach($count['loantapLoanData'] as $item)
                        <tr>
                            <td>{{ $item->loan_status }}</td>
                            <td>{{ indianFormat($item->total)  }}</td>
                            <td>{{ formatIndianCurrency($item->bank_sanction_amount , 2) }}</td>
                            <td>{{ formatIndianCurrency($item->nbfc_sanction_amount, 2) }}</td>
                        </tr>
                     @endforeach


                    </tbody>
                </table>
            </div>
        </div>
        </div>

@endsection

