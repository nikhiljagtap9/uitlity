

<!DOCTYPE html>

<html lang="en">
	<!--begin::Head-->
	<head><base href="">
		<title>-- Uitilty --</title>
		<meta charset="utf-8">
        <meta name="viewport"
        content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height" />
        <meta name="mobile-web-app-capable" content="yes">
		<link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Page Vendor Stylesheets(used by this page)-->
		<link href="/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
		<link href="/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Page Vendor Stylesheets-->
		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
		<style>
			.bg-light-blue {
				background-color: #d9edf7; /* Choose a soft blue shade */
				color: #000; /* Optional: Ensure text is readable */
		      }
				a.text-warning{
				color: #0065FF;
				}
							.image-button {
				position: absolute; /* Position relative to the container */
				top: 50%; /* Center vertically */
				right: 10px; /* Align to the right with a small margin */
			
				background-color: orange; /* Button background */
				color: #fff; /* Button text color */
				border: none; /* Remove border */
				border-radius: 5px; /* Optional: Rounded corners */
				cursor: pointer; /* Pointer cursor on hover */
				font-size: 16px; /* Adjust font size */
				transition: opacity 0.3s; /* Smooth hover effect */
				margin-top: 90px;
			}
			.hover-effect {
          		transition: background-color 0.3s ease; /* Smooth transition */
           	}

			.hover-effect:hover {
				background-color: #0065FF; /* Set your desired hover color */
				color: white; /* Optional: Change text color on hover */
			}

			.hover-effect:hover a {
				color: white; /* Optional: Change link color on hover */
			}

			.card-item {
						transition: all 0.3s ease-in-out;
			}
			/* Default image for all cards */
			/* Default image for all cards */
			.hover-image {
				transition: opacity 0.3s ease; /* Smooth transition */
			}

			/* Custom hover images for each card */
			.card-1:hover .hover-image {
				content: url('/assets/media/white_equifax.png');
			}

			.card-2:hover .hover-image {
				content: url('/assets/media/white_kyc.png');
			}

			.card-3:hover .hover-image {
				content: url('/assets/media/white_udam.png');
			}

			.card-4:hover .hover-image {
				content: url('/assets/media/white_voter.png');
			}

			.card-5:hover .hover-image {
				content: url('/assets/media/white_linence.png');
			}

			.active{
				transition: color .2s ease, background-color .2s ease;
    			background-color: #EAF2FF;
    			color: gray;
			}

		</style>
        @yield('head')
	</head>
	<!--end::Head-->



<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
		<div class="d-flex flex-column flex-root border">
			<!--begin::Page-->
			<div class="page d-flex flex-row flex-column-fluid">
				<!--begin::Aside-->
				<div id="kt_aside" class="aside aside-dark" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}"
				 data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
					 <!--begin::Brand-->
					 <div class="aside-logo flex-column-auto" id="kt_aside_logo">
						 <!--begin::Logo-->
						 <a href="{{ route('dashboard') }}">
							 <img alt="Logo" src="\assets\media\logos\logo.jpg" class="h-50px logo" />
						 </a>
						 <!--end::Logo-->
						 <!--begin::Aside toggler-->
						 <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
							 <!--begin::Svg Icon | path: icons/duotune/arrows/arr079.svg-->
							 <span class="svg-icon svg-icon-1 rotate-180">
									 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
										 <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor" />
										 <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor" />
									 </svg>
								 </span>
							 <!--end::Svg Icon-->
						 </div>
						 <!--end::Aside toggler-->
					 </div>
					 <div class="aside-menu flex-column-fluid">
						 <!--begin::Aside Menu-->
						<div class=" my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
						 data-kt-scroll-offset="0">
							 <!--begin::Menu-->
							 <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true" data-kt-menu-expand="false">
									<div class="menu-item  {{ request()->routeIs('dashboard') ? 'active' : '' }}" >
                                        <a href="{{ route('dashboard') }}">
                                            <span class="menu-link">
                                                    <span class="menu-icon">
                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                                        <span class="svg-icon svg-icon-2">
                                                            <i class="fas fa-tachometer-alt"></i>
                                                        </span>
                                            <!--end::Svg Icon-->
                                            </span>
                                            <span class="menu-title">Dashboards</span>
                                        </a>
									</div>
									<div class="menu-item {{ request()->routeIs('createcsv') ? 'active' : '' }}" >
                                        <a href="{{ route('createcsv') }}">
                                            <span class="menu-link">
                                                    <span class="menu-icon">
                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                                        <span class="svg-icon svg-icon-2">
                                                            <i class="fas fa-upload"></i>
                                                    </span>
                                            <!--end::Svg Icon-->
                                            </span>
										    <span class="menu-title">Excel to CSV Convert/Download</span>
                                        </a>
									</div>
									<div class="menu-item {{ request()->routeIs('disbursement.create') ? 'active' : '' }}" >
                                        <a href="{{ route('disbursement.create') }}">
                                            <span class="menu-link">
                                                    <span class="menu-icon">
                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                                        <span class="svg-icon svg-icon-2">
                                                            <i class="fas fa-cogs"></i>
                                                    </span>
                                            <!--end::Svg Icon-->
                                            </span>
                                            <span class="menu-title">Bulk Upload</span>
                                        </a>
									</div>
									<div class="menu-item {{ request()->routeIs('disbursebatch.index') ? 'active' : '' }}">
                                        <a href="{{ route('disbursebatch.index') }}">
                                            <span class="menu-link">
                                                    <span class="menu-icon">
                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                                        <span class="svg-icon svg-icon-2">
                                                            <i class="fas fa-chart-bar"></i>
                                                        </span>
                                            <!--end::Svg Icon-->
                                            </span>
                                            <span class="menu-title">Batche List</span>
                                        </a>
									</div>
							 </div>
							 <!--end::Menu-->
						</div>
						 <!--end::Aside Menu-->
					</div>
					 <div class="aside-footer flex-column-auto pt-5 pb-7 px-5" id="kt_aside_footer">
						<a href="{{ route('logout') }}" class="btn btn-custom btn-primary w-100"data-bs-dismiss-="click" title="Logout">
							<span class="btn btn-primary menu-link py-3 bg-light-blue">Logout</span>
							<!--begin::Svg Icon | path: icons/duotune/general/gen005.svg-->
							<span class="svg-icon btn-icon svg-icon-2">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
										<path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22ZM12.5 18C12.5 17.4 12.6 17.5 12 17.5H8.5C7.9 17.5 8 17.4 8 18C8 18.6 7.9 18.5 8.5 18.5L12 18C12.6 18 12.5 18.6 12.5 18ZM16.5 13C16.5 12.4 16.6 12.5 16 12.5H8.5C7.9 12.5 8 12.4 8 13C8 13.6 7.9 13.5 8.5 13.5H15.5C16.1 13.5 16.5 13.6 16.5 13ZM12.5 8C12.5 7.4 12.6 7.5 12 7.5H8C7.4 7.5 7.5 7.4 7.5 8C7.5 8.6 7.4 8.5 8 8.5H12C12.6 8.5 12.5 8.6 12.5 8Z" fill="currentColor" />
										<rect x="7" y="17" width="6" height="2" rx="1" fill="currentColor" />
										<rect x="7" y="12" width="10" height="2" rx="1" fill="currentColor" />
										<rect x="7" y="7" width="6" height="2" rx="1" fill="currentColor" />
										<path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor" />
									</svg>
								</span>
						</a>
					</div>
					 <!--end::Footer-->
				 </div>
					<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper" style="margin-top: -70px;">
						<!--begin::Header-->
						<div id="kt_header" class="header align-items-stretch">
							<!--begin::Container-->
							<div class="container-fluid d-flex align-items-stretch justify-content-between">
								<!--begin::Aside mobile toggle-->
								<div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Show aside menu">
									<div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_aside_mobile_toggle">
										<!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
										<span class="svg-icon svg-icon-1">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="currentColor" />
												<path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="currentColor" />
											</svg>
										</span>
										<!--end::Svg Icon-->
									</div>
								</div>
								<!--end::Aside mobile toggle-->
								<!--begin::Mobile logo-->
								<div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
									<a href="{{ route('dashboard') }}" class="d-lg-none">
										<img alt="Logo" src="\assets\media\logos\logo.jpg" class="h-30px" />
									</a>
								</div>
								<!--end::Mobile logo-->
								<!--begin::Wrapper-->
								<div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
									<!--begin::Navbar-->
									<div class="d-flex align-items-stretch" id="kt_header_nav">
										<!--begin::Menu wrapper-->
										<div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
											<!--begin::Menu-->
											<div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch" id="#kt_header_menu" data-kt-menu="true">
												<div data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" class="menu-item here show menu-lg-down-accordion me-lg-1">
													<h5>Hello, {{ ucwords(strtolower(Auth::user()->name)) }} </h5>	
												</div>
												
											</div>
											<!--end::Menu-->
										</div>
										<!--end::Menu wrapper-->
									</div>
									<!--end::Navbar-->
									
								</div>
								<!--end::Wrapper-->
							</div>
							<!--end::Container-->
						</div>
						<!--end::Header-->
						<!--begin::Content-->
						<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                            <!--begin::Post-->
                            <div class="post d-flex flex-column-fluid" id="kt_post">
								<!--begin::Container-->
								<div id="kt_content_container" class="container-xxl">
                                    @yield('content')
                                </div>
								<!--end::Container-->
							</div>
							<!--end::Post-->
						</div>
						<!--end::Content-->
						<!--begin::Footer-->
						<div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
							<!--begin::Container-->
							<div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
								<!--begin::Copyright-->
								<div class="text-dark order-2 order-md-1">
									<span class="text-muted fw-bold me-1"></span>
								</div>
							</div>
							<!--end::Container-->
						</div>
						<!--end::Footer-->
					</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--begin::Javascript-->
		<script>var hostUrl = "assets/";
					document.getElementById("viewToggleButton").addEventListener("click", function () {
					const hiddenCards = document.querySelectorAll(".card-item[style*='display: none']");
					const visibleCards = document.querySelectorAll(".card-item:not([style*='display: none'])");

					if (hiddenCards.length > 0) {
						// Show hidden cards
						hiddenCards.forEach(card => {
							card.style.display = "block";
						});
						this.textContent = "View Less"; // Change button text
					} else {
						// Hide extra cards (only keep first 4 visible)
						visibleCards.forEach((card, index) => {
							if (index >= 4) card.style.display = "none";
						});
						this.textContent = "View More"; // Change button text
					}
				});
		</script>
		<!--begin::Global Javascript Bundle(used by all pages)-->
		
		<script src="/assets/plugins/global/plugins.bundle.js"></script>
		<script src="/assets/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Page Vendors Javascript(used by this page)-->
		<script src="/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
		<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
		<!--end::Page Vendors Javascript (pagination)-->
		<script src="assets/js/custom/apps/ecommerce/catalog/products.js"></script>
		<!--begin::Page Custom Javascript(used by this page)-->
		<script src="/assets/js/widgets.bundle.js"></script>
		<script src="/assets/js/custom/widgets.js"></script>
		<script src="/assets/js/custom/apps/chat/chat.js"></script>
		<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
		<script src="/assets/js/custom/utilities/modals/create-app.js"></script>
		<script src="/assets/js/custom/utilities/modals/users-search.js"></script>
		<!--end::Page Custom Javascript-->
		<!--end::Javascript-->
		<!-- Bootstrap 4  -->
		<script src="{{ env('BASE') . '/all-files/plugins/bootstrap/js/bootstrap.bundle.min.js' }}"></script>
		<script src="{{ env('BASE') . '/all-files/dist/js/adminlte.js' }}"></script> 
		@yield('script')
	</body>



</html>
