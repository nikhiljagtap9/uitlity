@extends('layout.app')
@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">Bulk Upload (Upload CSV File to Verify Data)</h3>
    </div>

    <div class="card-body">
         <!--begin::Form-->
         <form id="kt_ecommerce_settings_general_form" class="form" action="{{ route('disbursement.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="partner_id" value="{{ env('PARTNER_ID', 'utility') }}">
            <div class="row mb-3" style="display:none">
                <!--begin::Label-->
                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                    <span class="required">Product</span>
                </label>
                <!--end::Label-->
                <!--begin::Select2-->
                <select name="product_id" id="product_id" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Select Product">
                    <option value=""></option>
                    <option value="utility" selected="selected">Utility</option>
                </select>
                <!--end::Select2-->
            </div>
            <div class="row mb-3">
                <label class="fs-6 fw-bold mb-2">
                    <span class="required">Upload CSV File</span>
                </label>
                <div class="uppy-wrapper uppy">
                    <div class="uppy-Root uppy-FileInput-container">
                        <input class="uppy-FileInput-input uppy-input-control" type="file" name="upload" id="kt_uppy_5_input_control">
                        <label class="uppy-input-label btn btn-light-primary btn-bold" for="kt_uppy_5_input_control">
                        Choose Files</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" id="upload" class="btn btn-primary">Upload</button>
            </div>
        </form>                       
        <!--end::Form-->

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
<!--end::Col-->
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const submitButtons = form.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(function(button) {
                        button.disabled = true; // Disable the button
                        button.innerText =
                            "Processing..."; // Optional: Show a loading message
                });
            });
        });

        // File validation for CSV
        document.getElementById('kt_uppy_5_input_control').addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.type !== 'text/csv') {
                alert('Please upload a valid CSV file.');
                this.value = ''; // Clear the input
            }
        });
    });
</script>
@endsection