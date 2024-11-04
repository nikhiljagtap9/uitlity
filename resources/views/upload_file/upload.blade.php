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
                <div class="form-group">
                    <label for="upload">Upload Excel File</label>
                    <input type="file" name="upload" id="upload" class="form-control" required>
                </div>
                <!-- Row for both buttons: Upload and Download Sample File -->
                <div class="form-group row">
                    <div class="col-auto">
                        <input type="submit" name="upload" class="btn btn-primary btn-block" value="Upload">
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('download.sample') }}" class="btn btn-primary custom-btn">Download Sample File</a>
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

