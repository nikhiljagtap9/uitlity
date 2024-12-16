@extends('layout.app')
@section('content')
    <!--begin::Row-->
    <div class="row g-xl-8">
        <!--begin::Col-->
        <div class="col-xl-8">
            <!--begin::Mixed Widget 7-->
            <!--begin::Body-->
            <div class="card card-xl-stretch-20 mb-5 mb-xl-8">
                <!--begin::Body-->
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bolder text-dark mb-0">API Type</h5>
                        <span id="viewToggleButton" class="text-primary cursor-pointer"><u><b>View More</b></u></span>
                    </div>
                    <div class="row" id="cardContainer">
                        <div class="text-center mt-4">
                        </div>
                        <!-- Card 1 -->
                        <div class="col-xxl-2 col-md-3 col-sm-6 card-item">
                            <div
                                class="bg-light-blue px-6 py-8 rounded-2 d-flex align-items-center justify-content-center flex-column hover-effect card-1">
                                <img src="assets/media/blue_equifax.png" alt="API Icon" width="25" height="25"
                                    class="mb-2 hover-image">
                                <a href="#" class="fw-bold fs-6 text-center">Pan NSDL</a>
                            </div>
                        </div>
                        <!-- Card 2 -->
                        <div class="col-xxl-2 col-md-3 col-sm-6 card-item">
                            <div
                                class="bg-light-blue px-6 py-8 rounded-2 d-flex align-items-center justify-content-center flex-column hover-effect card-2">
                                <img src="assets/media/blue_kyc.png" alt="Moped Icon" width="25" height="25"
                                    class="mb-2 hover-image">
                                <a href="#" class="fw-bold fs-6 text-center">CKYCL</a>
                            </div>
                        </div>
                        <!-- Card 3 -->
                        <div class="col-xxl-2 col-md-3 col-sm-6 card-item">
                            <div
                                class="bg-light-blue px-6 py-8 rounded-2 d-flex align-items-center justify-content-center flex-column hover-effect card-3">
                                <img src="assets/media/blue_udam.png" alt="Compliance Icon" width="25" height="25"
                                    class="mb-2 hover-image">
                                <a href="#" class="fw-bold fs-6 text-center">Udhyam</a>
                            </div>
                        </div>
                        <!-- Card 4 -->
                        <div class="col-xxl-2 col-md-3 col-sm-6 card-item">
                            <div
                                class="bg-light-blue px-6 py-8 rounded-2 d-flex align-items-center justify-content-center flex-column hover-effect card-4">
                                <img src="assets/media/blue_voter.png" alt="Human Resource Icon" width="25"
                                    height="25" class="mb-2 hover-image">
                                <a href="#" class="fw-bold fs-6 text-center">Voter Id</a>
                            </div>
                        </div>
                        <br>
                        <!-- Card 5 -->
                        <div class="col-xxl-2 col-md-3 col-sm-6 card-item" style="display: none;padding-top: 10px;">
                            <div
                                class="bg-light-blue px-6 py-8 rounded-2 d-flex align-items-center justify-content-center flex-column hover-effect card-5">
                                <img src="assets/media/blue_lisence.png" alt="Compliance Icon" width="25" height="25"
                                    class="mb-2 hover-image">
                                <a href="#" class="fw-bold fs-6 text-center">Driving License</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-xl-stretch-20 mb-5 mb-xl-8">
                <!--begin::Body-->
                <div class="card-body py-3">
                    <h5 class="card-title fw-bolder text-dark">API Overview</h5>
                    <div class="row">
						<!-- Card 1 -->
						<div class="col-12 col-md-4 position-relative">
							<div class="bg-light-blue px-6 py-8 rounded-2 d-flex align-items-center justify-content-center flex-column h-100 position-relative">
								<!-- SVG Icon at Top-Right
								<a href="{{route('genereateCSV')}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#009ef7" width="24px" height="24px" 
										class="position-absolute" style="top: 10px; right: 10px;">
										<path d="M5 20h14v-2H5v2zm7-18c-1.1 0-2 .9-2 2v8H8l4 4 4-4h-2V4c0-1.1-.9-2-2-2z"/>
									</svg>
							    </a> -->
								<!-- Label and Count -->
								<span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                    <a href="#" class="card-title fw-bolder text-dark">Total</a>
                                </span>
                                <a class="card-title  fs-1 fw-bolder text-dark">{{$count['allcount']}}</a>
							</div>
						</div>	
                        <!-- Card 2 -->
                        <div class="col-12 col-md-4 position-relative">
                            <div class="bg-light-blue px-6 py-8 rounded-2 d-flex align-items-center justify-content-center flex-column h-100 position-relative">
                                <!-- SVG Icon at Top-Right
								<a href="{{route('genereateCSV', ['status'=>'approved'])}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#009ef7" width="24px" height="24px" 
										class="position-absolute" style="top: 10px; right: 10px;">
										<path d="M5 20h14v-2H5v2zm7-18c-1.1 0-2 .9-2 2v8H8l4 4 4-4h-2V4c0-1.1-.9-2-2-2z"/>
									</svg>
							    </a>  -->
								<!-- Label and Count -->
								<span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                    <a href="#" class="card-title fw-bolder text-dark">Approved</a>
                                </span>
                                <a class="card-title  fs-1 fw-bolder text-dark">{{$count['approved']}}</a>
                            </div>
                        </div>
                        <!-- Card 3 -->
                        <div class="col-12 col-md-4 position-relative">
                            <div class="bg-light-blue px-6 py-8 rounded-2 d-flex align-items-center justify-content-center flex-column h-100 position-relative">
                                <!-- SVG Icon at Top-Right 
								<a href="{{route('genereateCSV', ['status'=>'rejected'])}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#009ef7" width="24px" height="24px" 
										class="position-absolute" style="top: 10px; right: 10px;">
										<path d="M5 20h14v-2H5v2zm7-18c-1.1 0-2 .9-2 2v8H8l4 4 4-4h-2V4c0-1.1-.9-2-2-2z"/>
									</svg>
							    </a> -->
								<!-- Label and Count -->
								<span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                    <a href="#" class="card-title fw-bolder text-dark">Rejected</a>
                                </span>
                                <a class="card-title fs-1 fw-bolder text-dark">{{$count['rejected']}}</a>
                            </div>
                        </div>
					</div>
					<div class="row my-2" style="display:none;">	
						<!-- Card 4 -->
						<div class="col-12 col-md-4 position-relative">
                            <div class="bg-light-blue px-6 py-8 rounded-2 d-flex align-items-center justify-content-center flex-column h-100 position-relative">
                                <!-- SVG Icon at Top-Right 
								<a href="{{route('genereateCSV', ['status'=>'pending'])}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#009ef7" width="24px" height="24px" 
										class="position-absolute" style="top: 10px; right: 10px;">
										<path d="M5 20h14v-2H5v2zm7-18c-1.1 0-2 .9-2 2v8H8l4 4 4-4h-2V4c0-1.1-.9-2-2-2z"/>
									</svg>
							    </a> -->
								<!-- Label and Count -->
								<span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                    <a href="#" class="card-title fw-bolder text-dark">Pending Accounts</a>
                                </span>
                                <a class="card-title fs-1 fw-bolder text-dark">{{$count['pending']}}</a>
                            </div>
                        </div>		
						<!-- Card 5 -->
						<div class="col-12 col-md-4 position-relative" >
                            <div class="bg-light-blue px-6 py-8 rounded-2 d-flex align-items-center justify-content-center flex-column h-100 position-relative">
                                <!-- SVG Icon at Top-Right 
								<a href="{{route('genereateCSV', ['status'=>'duplicate'])}}">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#009ef7" width="24px" height="24px" 
										class="position-absolute" style="top: 10px; right: 10px;">
										<path d="M5 20h14v-2H5v2zm7-18c-1.1 0-2 .9-2 2v8H8l4 4 4-4h-2V4c0-1.1-.9-2-2-2z"/>
									</svg>
							    </a> -->
								<!-- Label and Count -->
								<span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                    <a href="#" class="card-title fw-bolder text-dark">Duplicate Accounts</a>
                                </span>
                                <a class="card-title fs-1 fw-bolder text-dark">{{$count['duplicate']}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="col-xl-4" >
			<!--begin::List Widget 5-->
			<div class="card card-xl-stretch-40">
				<img src="assets/media/sider.png" alt="Image Description" class="img-fluid" />
			</div>
			<!--end: List Widget 5-->
		</div>
        <div class="col-xl-12">
            <!--begin::List Widget 5-->
            <div class="card card-xl-stretch-40 image-container" style="margin-top: -40px;">
                <img src="assets/media/banner.png" alt="Image Description" class="img-fluid" />
                <button class="image-button">Know more</button>
            </div>
            <!--end: List Widget 5-->
        </div>
        <!--end::Col-->
    </div>
@endsection
