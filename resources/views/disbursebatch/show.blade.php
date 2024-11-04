@extends('layout.app')
@section('head')
	<link rel="stylesheet" href="{{ env('BASE').('/all-files/plugins/dataTable/datatables.min.css') }}" type="text/css">


    <style>
        * {
            box-sizing: border-box;
	}

         .fade {
            opacity: 1 !important;
        }

        .modal.fade .modal-dialog {
            transform: translate(0, 10%);
        }


        body {
            background: #e7e7e7;
        }

        .tab {
            width: 100%;
            overflow: hidden;
        }

        .tab button {
            background-color: hsl(210, 17%, 93%);
            border: none;
            outline: none;
            cursor: pointer;
            padding: 10px 16px;
            font-weight: bold;
            transition: 0.3s;
        }

        .tab button:hover {
            background-color: #2289bf;
        }

        .tab button.active {
            /*background-color: #fff; */
            background-color: #4491d0;
            /* Primary blue for active tab */
            color: rgb(4, 1, 1);
        }

        .tabcontent {
            background: #fff;
            width: 100%;
            display: none;
            padding: 6px 12px;
            font-family: arial;
            line-height: 21px;
	}


              /* Styles specific to the filter-form */
.filter-form {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 15px; /* Increased gap between form groups */
    flex-wrap: wrap;
    padding: 5px;
}

.filter-form .form-group {
    display: flex;
    align-items: center;
    gap: 15px; /* Gap between label and input field */
}

.filter-form label {
    font-size: 16px;
    color: black;
    margin-right: 10px; /* Increased space between label and input field */
}

.filter-form select,
.filter-form input[type="number"] {
    width: 150px; /* Adjust input width */
    height: 30px;
    font-size: 13px;
    padding: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
    background-color: #fff;
    font-color: black;
}

.filter-form button {
    width: 150px; /* Increased button width */
    height: 30px; /* Increased button height */
     background-color: #2699d5; 
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    border-color:#2289bf;
}


select {
    color: black !important; /* Always have black font color */
    background-color: white; /* Make sure the background is white */
}
select:focus,
select option {
	color: black !important;
}

/* Remove the default gray color for the placeholder (Select) option */
select option:checked {
    color: black !important; /* The "Select" placeholder will also appear black */
}

/* Ensure that the font remains black when an option is selected or when focused */
select:focus,
select option:checked {
    color: black !important;
}
#example th{
	 text-align:left;
	padding:8px;
	color:#fff !important;
}

#example2 th{
         text-align:left;
	padding:8px;
        
	color:#fff !important;
}


#example2 td {
	text-align:left;
	padding:8px;
}

#example td {
        text-align:left;
        padding:8px;
}




    </style>

@endsection
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
    @if ($batch['status'] == 'Pending')
    <div class="alert alert-warning">
        <p> Batch Pending for BRE Process</p>
    </div>
    @endif
    @if ($batch['status'] == 'Approved')
    <div class="alert alert-warning">
        <p> Batch Pending for CBS Process</p>
    </div>
    @endif

</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">
            Disbursement Batch Details
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-12">

                <table class="table table-stripped table-bordered">
                    <thead>
                        <tr>
                            <th>Batch No</th>
                            <th>No. of Loans</th>
                            <th>Total Loan Amount</th>
                            <th>Total Sanction Amount</th>
                            <th>Total Nbfc Sanction Amount</th>
                            <th>Total Bank Sanction Amount</th>

                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>{{ $batch->uuid }}</td>
                            <td>{{ $collection_count }}</td>
                            <td>₹{{ number_format($batch->total_loan_amount, 2) }}</td>
                            <td>₹{{ number_format($batch->total_sanction_amount, 2) }}</td>
                            <td>₹{{ number_format($batch->nbfc_sanction_amount, 2) }}</td>
                            <td>₹{{ number_format($batch->bank_sanction_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-12">

                <table class="table table-stripped table-bordered">
                    <thead>
                        <tr>
                            <th>Business Type</th>
                            <th>Total Loan Amount</th>
                            <th>Bank Loan Amount</th>
                            <th>NBFC Loan Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td>{{ $result->Business_Type }}</td>
                            <td>₹{{ number_format($result->sanction_amount, 2) }}</td>
                            <td>₹{{ number_format($result->bank_sanction_amount, 2) }}</td>
                            <td>₹{{ number_format($result->nbfc_sanction_amount, 2) }}</td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">
            BRE Details
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-12">
                <div class="brand-white-bg">
                    <table class="table table-stripped table-bordered">
                        <thead>
                            <tr>
                             Loan Account Number   <th>No. of Loans</th>
                                <th>Total Loan Amount</th>
                                <th>Total Sanction Amount</th>
                                <th>Total Nbfc Sanction Amount</th>
                                <th>Total Bank Sanction Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bre_results as $result)
                            <tr>
                                <td>{{ $result->status_count }}</td>
                                <td>₹{{ number_format($result->sanction_amount, 2) }}</td>
                                <td>₹{{ number_format($result->sanction_amount, 2) }}</td>
                                <td>₹{{ number_format($result->nbfc_sanction_amount, 2) }}</td>
                                <td>₹{{ number_format($result->bank_sanction_amount, 2) }}</td>
                                <td>{{ $result->status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    @if ($batch['status'] == 'Pending' && $pending_count > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Run BRE
            </h3>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-12 col-sm-12 col-md-12">
                    <button class="btn btn-success" id="start-processing">Start Processing</button>
                    <div id="progress">Progress: 0%</div>
                </div>

                <div class="col-12 col-sm-12 col-md-12">
                    <div class="progress" style="width: 100%; height:20px; padding:0;">
                        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;"
                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif
    @if ($batch['status'] == 'Approved' && $approved_count>0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                CBS Process
            </h3>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-3 col-sm-3 col-md-3">
                    <form id = 'disbursement' action="{{ route('disbursment') }}" class="form-group" method="POST">
                        @csrf
                        <input type="hidden" name="batch_id" value="{{ $batch->uuid }}">
                    </form>
 <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#confirmationModal" data-backdrop = "false">Disburse to Escrow
                                    Account</button>	    
	</div>
                <div class="col-3 col-sm-3 col-md-3">

                </div>
                <div class="col-3 col-sm-3 col-md-3" style="display: none;">
                    <form action="{{ route('cbsapi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="batch_id" value="{{ $batch->uuid }}">
                        <input type="hidden" name="cbs_api" value="escrow">
                        <button type="submit" class="btn btn-primary" name="submit">Credit To Escrow
                            Account</button>
                    </form>
            </div>
	    
	</div>
                <div class="col-3 col-sm-3 col-md-3"></div>
            </div>
        </div>
    </div>
    @endif  







  <div class="card">
        <div class="card-header">
            
       

        <div class="tab">
                <button class="tablinks" data-tab="tab1" onclick="openTab(event, 'tab1')" {{ $currentTab === 'tab1' ? 'class=active' : '' }}>Approved List</button>
                <button class="tablinks" data-tab="tab2" onclick="openTab(event, 'tab2')" {{ $currentTab === 'tab2' ? 'class=active' : '' }}>Rejected List</button>
              <!--  <button class="tablinks" data-tab="tab3">Disbursement Status</button>   -->

            </div>
       </div>


        <div id="tab1" class="tabcontent" style="{{ $currentTab === 'tab1' ? 'display:block' : 'display:none' }}">
          <div class="row mt-3">
	     <div class="col-12">

	        @if ($approvedList->isEmpty())
                        <tr>
                            <td colspan="6">No data available.</td>
			 </tr>
                @else
                <table id="example" class="table table-stripped table-bordered">
                    <thead>
                        <tr>
                            <th>NBFC Reference No</th>
                            <th>Customer Name</th>
                            <th>Loan Amount</th>
                            <th>Sanction Amount</th>

                            <th>Bank Sanction Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                     <!--    @if ($approvedList->isEmpty())
                                    <tr>
                                        <td colspan="6">No data available.</td>
                                    </tr>
                                @else   -->
                                    @foreach ($approvedList as $approved)
                                        <tr>
                                            <td>{{ $approved->NBFC_Reference_Number }}</td>
                                            <td>{{ $approved->customer_name }}</td>
                                            <td>{{ $approved->loan_amount }}</td>
                                            <td>{{ $approved->sanction_amount }}</td>
					    <td>{{ $approved->bank_sanction_amount }}</td>
                                            <td>{{ $approved->status }}</td>
                                        </tr>
                                    @endforeach
                             <!--   @endif   -->
                    </tbody>
                </table>
		@endif

            </div>
        </div>
 </div>

<div id="tab2" class="tabcontent" style="{{ $currentTab === 'tab2' ? 'display:block' : 'display:none' }}">
                <div class="row mt-3">
		    <div class="col-12">


		      <form method="GET" action="{{ route('disbursebatch.getData', ['disbursebatch' => $batch->uuid]) }}"  class="filter-form"  >
                        <input type="hidden" name="current_tab" id="currentTab" value="{{ $currentTab }}"> <!-- Pass the current tab -->

                        <label for="type">Select Type:</label>
                        <select name="type" id="type" required>
                            <option value="" disabled {{ request('type') === null ? 'selected' : '' }}>Select</option>
                            <option value="pan" {{ request('type') === 'pan' ? 'selected' : '' }}>PAN Match Score</option>
                            <option value="ckyc" {{ request('type') === 'ckyc' ? 'selected' : '' }}>CKYC Match Score</option>
                            <option value="udyam" {{ request('type') === 'udyam' ? 'selected' : '' }}>Udyam Match Score</option>
                        </select>

                        <label for="lower_value">Range:</label>
                        <input type="number" name="lower_value" placeholder="Min Value" value="{{ request('lower_value') }}" required>

                        <label for="upper_value">Range:</label>
                        <input type="number" name="upper_value" placeholder="Max Value" value="{{ request('upper_value') }}" required>

			<button type="submit" class="btn-primary">Search</button>
	                 <button type="button" class="reset-button btn-primary" onclick="resetFilters()">Reset</button><br><br>	
                    </form>



                    @if ($rejectedList->isEmpty())
                             <tr>
                                  <td colspan="6">No data available.</td>
                             </tr>
		    @else
		      <form id="approveForm" method="GET" action="{{ route('processRejectedChunks') }}">  			    
			@if ($batch->status != 'Disbursed')
			<br>
			<div class="card-body">

            		<div class="row">
                		<div class="col-12 col-sm-12 col-md-12">
<button id = 'approve_button'  type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#confirmationModal1" data-backdrop = "false" >Approve Selected Loans</button>		
                    	<div id="progress">Progress: 0%</div>
                	</div>

                	<div class="col-12 col-sm-12 col-md-12">
                       <div class="progress" style="width: 100%; height:20px; padding:0;">
                        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;"
                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    	</div>
                	</div>

            	</div>
        	</div>
			@endif
			<table id="example2" class="table table-striped table-bordered">
                            <thead>
				<tr>
				   @if ($batch->status != 'Disbursed')
					 <th><input type="checkbox" id="selectAll"></th>
				  
					@endif
                                    <th>NBFC Reference No</th>
                                    <th>Customer Name</th>
                                  <!--  <th>Loan Amount</th> -->
                                    <th>Sanction Amount</th>
				    <!-- <th>Bank Sanction Amount</th> -->
				    <th> Message </th>
				    <th> PAN Match(%) </th>
				    <th> CKYC Match(%) </th>
				    <th> Udyam Match(%) </th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                       <!--         @if ($rejectedList->isEmpty())
                                    <tr>
                                        <td colspan="6">No data available.</td>
                                    </tr>
                                @else  -->
                                    @foreach ($rejectedList as $rejected)
					<tr>
					    @if($batch->status !='Disbursed')
					     <td><input type="checkbox" name="selected_loans[]" class="loanCheckbox" value="{{ $rejected->NBFC_Reference_Number  }}"></td> <!-- Checkboxes for each loan -->
					   @endif
					    <td>{{ $rejected->NBFC_Reference_Number  }}</td>
                                            <td>{{ $rejected->customer_name }}</td>
                                           <!--  <td>{{ $rejected->loan_amount }}</td>  -->
                                            <td>{{ $rejected->sanction_amount }}</td>
					  <!--  <td>{{ $rejected->bank_sanction_amount }}</td>  -->
					    <td>{{ $rejected->message }}   </td>
					   <td>@if($rejected->pan_match_score){{ $rejected->pan_match_score }} @else NA @endif</td>
                        <td>@if($rejected->ckyc_match_score) {{ $rejected->ckyc_match_score }} @else NA @endif</td>
                        <td>@if($rejected->udyam_match_score){{ $rejected->udyam_match_score }} @else NA @endif</td>
					    <td>{{ $rejected->status }}</td>
                                        </tr>
                                    @endforeach
                        <!--        @endif   -->
                            </tbody>
			</table>

                    </form>   
	            @endif
                    </div>
                </div>
	    </div>

</div>







	<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>

                        </div>
			<div class="modal-body">
			    <div class="alert alert-info" role="alert">
			    	<strong class = 'text-white'>Transaction Details :</strong>
			    </div>
				<table class="table table-stripped table-bordered">
                    		<thead>
                       		 <tr>
                            		<th>Business Type</th>
                           		<th>From</th>
                            		<th>To</th>
                            		<th>Amount</th>
                        	</tr>
                    		</thead>
                   		 <tbody>
                        @foreach ($results as $result)
                        <tr>
                            <td>{{ $result['Business_Type'] }}</td>
                            <td>{{$setting['loan_account_number_' . strtolower($result['Business_Type'])]}}</td>
                            <td>{{$setting['to_loan_account_number_' . strtolower($result['Business_Type'])]}}</td>
                            <td>₹{{$result['bank_sanction_amount']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                            <div class="alert text-danger" role="alert">
                                <strong>Are you sure you want to perform this action?</strong>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmSubmit">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
    @endsection
    @section('script')



<!-- DataTable -->
    <script src="{{  env('BASE').('/all-files/plugins/dataTable/datatables.min.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.22/sorting/date-de.js"></script>

    <script>
       function resetFilters(){
       		window.location.href = "{{ route('disbursebatch.getData', ['disbursebatch' => $batch->uuid]) }}?current_tab=tab2";
       }
    </script>
    <script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;

        // Hide all tab contents
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Remove the active class from all tab links
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show the current tab and add the active class to the clicked button
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";

        // Update the hidden input field with the current tab value
        document.getElementById("currentTab").value = tabName;
    }

    // Automatically open the correct tab based on currentTab value on page load
    document.addEventListener("DOMContentLoaded", function() {
        var currentTab = "{{ $currentTab }}";
        document.querySelector(`button[data-tab="${currentTab}"]`).click();
    });
    </script>


    <script>
            // Initialize DataTable when the document is ready
            $(document).ready(function() {
		$('#example').DataTable(); // Initialize DataTable tab 1
		$('#example2').DataTable(); // Initialize DataTable for Tab 2
                $('#example3').DataTable(); // Initialize DataTable for Tab 2

		// "Select All" functionality
                $('#selectAll').on('click', function() {
                    var isChecked = $(this).is(':checked');
                    $('.loanCheckbox').prop('checked', isChecked); // Set all checkboxes to match the header checkbox
                });

                // Update the "Select All" checkbox state if a user manually selects/deselects a checkbox
                $('.loanCheckbox').on('change', function() {
                    if ($('.loanCheckbox:checked').length === $('.loanCheckbox').length) {
                        $('#selectAll').prop('checked', true); // Set "Select All" to checked if all are checked
                    } else {
                        $('#selectAll').prop('checked', false); // Uncheck "Select All" if not all checkboxes are selected
                    }
                });

            });

            // Custom Tab Switching Logic
            var tabButtons = document.querySelectorAll(".tablinks");

            for (var i = 0; i < tabButtons.length; i++) {
                tabButtons[i].addEventListener("click", function() {
                    var tabName = this.dataset.tab;
                    var tabContent = document.getElementById(tabName);
                    var allTabContent = document.querySelectorAll(".tabcontent");
                    var allTabButtons = document.querySelectorAll(".tablinks");

                    // Hide all tabs and remove active class from buttons
                    for (var j = 0; j < allTabContent.length; j++) {
                        allTabContent[j].style.display = "none";
                    }
                    for (var k = 0; k < allTabButtons.length; k++) {
                        allTabButtons[k].classList.remove("active");
                    }

                    // Show the selected tab content and add active class to the button
                    tabContent.style.display = "block";
                    this.classList.add("active");
                });
            }

            // Show the first tab by default
            document.querySelector(".tablinks").click();
        </script>
	<script>
   		$(document).ready(function() {
    $('#confirmApprovedSubmit').click(function() {
        $('#confirmationModal1').modal('hide');
        var selected_loans = [];
        $('input[name="selected_loans[]"]:checked').each(function() {
            selected_loans.push($(this).val());
        });
        processRejectedChunk(0, selected_loans);
    });

    function processRejectedChunk(offset, selectedLoans) {
        $('#approve_button').hide();
        $.ajax({
            url: "{{ route('processRejectedChunks') }}",
            method: 'POST',
            data: {
                offset: offset,
                selectedLoans: selectedLoans,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Update the progress UI
                updateProgress(offset);

                if (response.moreData) {
                    processRejectedChunk(response.nextOffset, selectedLoans); // Process the next chunk
                } else {
                    $('#progress').text('Processing complete! Redirecting in 5 seconds...');
                    setTimeout(function() {
                        window.location.href = "{{ route('disbursebatch.show', [$batch]) }}";
                    }, 5000); 
                }
            },
            error: function() {
                $('#approve_button').show();
                $('#progress').text('An error occurred during processing.');
            }
        });
    }

    function updateProgress(offset) {
        const totalRecords = {{ $pending_count }}; // Update this with the total count
        const percentage = Math.min(100, (offset / totalRecords) * 100);
        $('#progress-bar').css('width', percentage.toFixed(2) + '%').attr('aria-valuenow', percentage.toFixed(2));
        $('#progress').text('Progress: ' + percentage.toFixed(2) + '%');
    }
});

	 </script>

    <script>
        $(document).ready(function() {
            $('#start-processing').click(function() {

                processChunk(0); // Start processing from offset 0
            });

	  //  $('#example').DataTable();
	    
	    function processChunk(offset) {
                $('#start-processing').hide();
                $.ajax({
                    url: "{{ route('processChunks') }}",
                    method: 'POST',
                    data: {
                        offset: offset,
                        batch_id: '{{ $batch->uuid }}',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Update the progress UI
                        updateProgress(offset);

                        if (response.moreData) {
                            // Continue processing the next chunk
                            processChunk(response.nextOffset);
                        } else {
                            // All done
                            $('#progress').text(
                                'Processing complete! Please wait... Redirecting in 5 seconds...');

                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('disbursebatch.show', [$batch]) }}";
                            }, 5000); // 10,000 milliseconds = 10 seconds

                        }
                    },
                    error: function() {
                        $('#start-processing').show();
                        $('#progress').text('An error occurred during processing.');
                    }
                });
            }

            function updateProgress(offset) {
                // Assuming you know the total number of records
                const totalRecords = {{$pending_count }};
                const percentage = Math.min(100, (offset / totalRecords) * 100);
                $('#progress-bar').css('width', percentage.toFixed(2) + '%').attr('aria-valuenow', percentage
                    .toFixed(2));
                $('#progress').text('Progress: ' + percentage.toFixed(2) + '%');
            }
        });
    </script>
	
	<script>
            // JavaScript to handle form submission when "Confirm" is clicked
            document.getElementById('confirmSubmit').addEventListener('click', function() {
                // Submit the form
                document.getElementById('disbursement').submit();
            });
	</script>

<div class="modal fade" id="confirmationModal1" tabindex="-1" aria-labelledby="confirmationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel1">Confirm Action</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>

                        </div>
                        <div class="modal-body">
                            <div class="alert text-danger" role="alert">
                                <strong>Are you sure you want to perform this action?</strong>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmApprovedSubmit">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>

        <script>
            // JavaScript to handle form submission when "Confirm" is clicked
        //    document.getElementById('confirmApprovedSubmit').addEventListener('click', function() {
              // Submit the form
          //    document.getElementById('approveForm').submit();
           // });
        </script>
    @endsection
