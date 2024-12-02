@extends('layout.app')
@section('content')
    <div class="brand-white-bg">
        @if (session('success'))
            <div class="alert alert-success">
                <p> {{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                <p> {{ session('error') }}</p>
            </div>
        @endif


    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Update Application ({{ $disbursement->lapp_id }})
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12">
                    <div class="brand-white-bg">
                        <form action="{{ route('disbursement.update', [$disbursement]) }}" method="POST"
                            enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                            <div class="col-6 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="utr_bom_pos_update">UTR No</label>
                                    <input type="text" name="utr_bom_pos_update" id="utr_bom_pos_update"
                                        class="form-control" value="{{ $disbursement->utr_bom_pos_update }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="upload_1">File 1</label>
                                    <input type="file" name="upload_1" id="upload_1" class="form-control">
                                    <a href="#">Download File</a>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="upload_2">File 2</label>
                                    <input type="file" name="upload_2" id="upload_2" class="form-control">
                                    <a href="#">Download File</a>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="upload_3">File 3</label>
                                    <input type="file" name="upload_3" id="upload_3" class="form-control">
                                    <a href="#">Download File</a>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="upload_4">File 4</label>
                                    <input type="file" name="upload_4" id="upload_4" class="form-control">
                                    <a href="#">Download File</a>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit" name="submit">Save</button>
                                </div>
                            </div>
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
                    //alert('click');
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
