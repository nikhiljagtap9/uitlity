<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Progress</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Job Progress</h2>
        <div class="progress">
            <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
        <p id="status" class="mt-3">Status: Pending</p>
    </div>

    <script>
        function checkJobStatus(jobId) {
            $.ajax({
                url: "{{ route('jobprogress', [$jobId]) }}",
                method: 'GET',
                success: function(data) {
                    $('#progress-bar').css('width', data.progress + '%').attr('aria-valuenow', data.progress)
                        .text(data.progress + '%');
                    $('#status').text('Status: ' + data.status);

                    if (data.status !== 'Completed') {
                        setTimeout(function() {
                            checkJobStatus(jobId);
                        }, 5000);
                    }
                },
                error: function() {
                    console.error('Failed to retrieve job status.');
                }
            });
        }

        // Example usage: Replace 'your-job-id' with the actual job ID passed from the controller
        $(document).ready(function() {
            var jobId = '{{ $jobId }}'; // This is the job ID passed from the controller
            checkJobStatus(jobId);
        });
    </script>
</body>

</html>
