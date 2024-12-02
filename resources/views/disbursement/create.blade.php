@extends('layout.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Create Application (Bulk Upload CSV File)
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">
                    <div class="row brand-white-bg">
                        <form action="{{ route('disbursement.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="partner_id" value="{{ env('PARTNER_ID', 'capwise') }}">
                            <div class="form-group">
                                <select name="product_id" id="product_id" class="form-control">
                                    <option value="capwise-business-loan">Capwise Business Loan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="file" name="upload" id="upload" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="submit" name="upload" id="upload" class="btn btn-primary"
                                    value="Upload">Upload</button>
                            </div>
                            @if ($errors->any())
                                <div>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-success">
                                    <p> {{ session('success') }}</p>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all forms on the page

            const forms = document.querySelectorAll('form');

            forms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    alert('click');
                    // Disable all submit buttons in the current form
                    const submitButtons = form.querySelectorAll('button[type="submit"]');
                    submitButtons.forEach(function(button) {
                        button.disabled = true; // Disable the button
                        button.innerText =
                            "Processing..."; // Optional: Show a loading message
                    });
                });
            });
        });
    </script>
@endsection
