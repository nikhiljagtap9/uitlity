@extends('layout.app')

@section('head')
    <style>
        /* Pop-up alert styles */
        .alert-box {
            display: none;
            position: fixed;
            top: 80px; /* Adjust this value to position it below your navbar */
            left: 50%;
            transform: translateX(-50%); /* Center horizontally */
            width: 600px; /* Set fixed width */
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            z-index: 1000;
            font-family: Arial, sans-serif;
        }

        .alert-header {
            background-color: #4491d0;
            color: white;
            padding: 10px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert-title {
            font-size: 18px;
            background-color: #4491d0;
        }

	.close-btn {
	    cursor: pointer;
            font-size: 20px;
	    font-weight: bold;
	    font-family:Arial, sans-serif;
        }

        .alert-body {
            padding: 20px;
            text-align: left;
            max-height: 300px; /* Set a maximum height */
            overflow-y: auto;
        }

        /* Button styles inside the alert */
        .alert-body .download-error-btn {
            margin-top: 15px;
            background-color: #4491d0;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        /* Style for highlighting the text */
            .highlight {
                font-weight: bold;
                color: red; /* You can change this color as per your requirement */
            }

            .custom-btn {
                width: 170px; /* Adjust as needed */
               /* background-color: DodgerBlue*/
            }


    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">Convert Excel File to CSV File</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('upload_excel') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="fs-6 mb-2">
                            <span class="required">Upload Excel File</span>
                        </label>
                        <div class="uppy-wrapper uppy">
                            <div class="uppy-Root uppy-FileInput-container">
                                <input class="uppy-FileInput-input uppy-input-control" type="file" name="upload" id="kt_uppy_5_input_control">
                                <label class="uppy-input-label btn btn-light-primary btn-bold" for="kt_uppy_5_input_control">
                                Choose Files</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="col">
                            <div class="card card-flush py-2">
                               
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-2">
                                        <!--begin::Dropzone-->
                                        <a href="{{ url('/download-sample-excel') }}">
                                            <div class="dropzone" id="kt_ecommerce_add_product_media">
                                                <!--begin::Message-->
                                                <div class="dz-message needsclick">
                                                    <div class="ms-4">
                                                        <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Download Sample File</h3>
                                                        <span class="fs-7 fw-bold text-gray-400">Acceptable formats: XLSX.</span>
                                                    </div>
                                                    <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                                                    <!--end::Info-->
                                                </div>
                                            </div>
                                        </a>
                                        <!--end::Dropzone-->
                                    </div>         
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row for both buttons: Upload and Download Sample File -->
                <div class="form-group row">
                    <div class="col-auto">
                        <input type="submit" name="upload" class="btn btn-primary btn-block" value="Upload">
                    </div>
                </div>
            </form>

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

    <!-- Pop-up Alert Box -->
    <div id="alertBox" class="alert-box">
        <div class="alert-header">
            <span class="alert-title">Error Occurred in File</span>
            <span class="close-btn" onclick="closeAlert()">&times</span>
        </div>
        <div class="alert-body">
            <p id="alertMessage">This is a professional alert message.</p>
            <!-- Download Error File button inside the alert -->
            <button id="downloadErrorBtn" class="download-error-btn" style="display:none;" onclick="window.location.href='{{ route('downloadErrorFile') }}'">Download Error File</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var errorMessage = {!! json_encode(session('error')) !!};
            var errorMessage1 = {!! json_encode(session('error1'))!!}
            var successMessage = {!! json_encode(session('success')) !!};

            if (errorMessage1) {
                showAlert(errorMessage1.replace(/\\n/g, '\n')); // Pass 'true' for error
            }

            if (errorMessage) {
                showAlert(errorMessage.replace(/\\n/g, '\n'), true); // Pass 'true' for error
            }

            if (successMessage) {
                showAlert(successMessage, false); // No download button for success messages
            }
        });

        function showAlert(message) {
            document.getElementById('alertMessage').innerText = message;
            document.getElementById('alertBox').style.display = 'block';
        }

        // Show the alert pop-up
        function showAlert(message, isError) {
            document.getElementById('alertMessage').innerHTML = message;
            document.getElementById('alertBox').style.display = 'block';

            // If it's an error, show the "Download Error File" button
            if (isError) {
                document.getElementById('downloadErrorBtn').style.display = 'inline-block';
            }
        }

        // Close the alert pop-up
        function closeAlert() {
            document.getElementById('alertBox').style.display = 'none';
        }
    </script>
@endsection

