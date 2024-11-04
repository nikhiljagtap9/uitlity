@extends('layout.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Upload Disbursement CSV File
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">
                    <div class="row brand-white-bg">
                        <form action="{{ route('uploadexcel') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            Upload Excel File
                            <div class="form-group">
                                <input type="file" name="upload" id="upload" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="upload" id="upload" class="btn btn-primary" value="Upload">
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

