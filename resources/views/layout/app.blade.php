<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height" />
    <meta name="mobile-web-app-capable" content="yes">
    <title>CAPRI Colending</title>
    <!-- Google Font: Montserrat -->
    <link rel="stylesheet" href="{{ env('BASE') . '/all-files/dist/css/montserrat-font-family.css' }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ env('BASE') . '/all-files/plugins/fontawesome-free/css/all.min.css' }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ env('BASE') . '/all-files/dist/css/adminlte.min.css' }}">
    <!--default loantap css-->
    <link rel="stylesheet" href="{{ env('BASE') . '/all-files/all-css.css' }}">
    @yield('head')
</head>

<body>
    <div class="wrapper">



        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>



            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <div class="user-panel d-flex nav-link pr-0 pl-0 align-items-center">
                        <div class="info">
                            <a href="#" class="d-block">Hello, {{ Auth::user()->name }} &nbsp;&nbsp;</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item">


                    <a class="btn btn-primary" href="{{ route('logout') }}">Logout</a>

                </li>
            </ul>
        </nav>

        <div class="page-wrapper">

            <style>
                body:not(.layout-fixed) .main-sidebar {
                    top: 50px !important;
                    /*position:fixed;*/
                }

                body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header {
                    -webkit-transition: none !important;
                    -moz-transition: none !important;
                    -ms-transition: none !important;
                    -o-transition: none !important;
                    transition: none !important;
                    margin-left: 0px !important;
                }

                .wrapper {
                    background: #F9F9F9;
                }

                .content-wrapper {
                    background: #F9F9F9;
                }

                .card {
                    /*
                    border: none !important;
                    border-radius: 0px !important;
                    background-color: transparent;
                    */
                    padding: 0 15px;
                    border-color: #F1F1F4;
                    box-shadow: 0px 3px 4px 0px rgba(0, 0, 0, 0.03);
                }

                .card-header {
                    margin: 20px 0;
                    border-bottom: 1px solid #F1F1F4;
                }

                .card-title {
                    font-size: 2rem;
                }

                .form-control {
                    height: auto;
                }

                table.table-bordered.dataTable th {
                    font-size: 1.5rem
                }

                table.table-bordered.dataTable a {
                    text-decoration: none;
                }

                .content-wrapper>.content {
                    padding: 0px !important;
                    margin-top: 0px !important;
                }

                .main-sidebar p {
                    color: #DEDEDE;
                }


                .nav-sidebar .nav-item>.nav-link {
                    font-size: 13px !important;
                }

                .sidebar .nav-link {
                    padding: 10px 10px !important;
                }

                .navbar {
                    margin-bottom: 10px !important;
                }

                .navbar-nav>li>a {
                    padding-top: 0px !important;
                    padding-bottom: 0px !important;
                }

                .main-sidebar li:hover,
                p:hover {
                    color: #37474F !important;
                }

                .active-header-menu p {
                    color: #37474F !important;
                }

                .dropdown-menu {
                    transform: translate3d(0px, 40px, 0px) !important;
                    width: 100% !important;
                    border: none !important;
                    border-radius: none !important;
                }

                .card-header {
                    padding-right: 0em !important;
                    padding-left: 0em !important;
                }

                #select_nbfc {
                    padding: 1.25rem;
                }

                @media(min-width:768px) {
                    body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header {
                        -webkit-transition: none !important;
                        -moz-transition: none !important;
                        -ms-transition: none !important;
                        -o-transition: none !important;
                        transition: none !important;
                        margin-left: 0px !important;
                    }
                }
            </style>






























            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color:#0484CC ">

                <!-- Brand Logo -->
                <div>
                    <a href="#" class="brand-link">

                        <img src="https://capwisefin.com/wp-content/uploads/2022/12/Capwise-White-Logo.png"
                            alt="AdminLTE Logo" class="brand-image float-none">

                    </a>
                </div>


                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar Menu active-header-menu-->
                    <nav class="mt-2">
                        <ul class="nav  nav-sidebar flex-column" data-widget="treeview" role="menu"
                            data-accordion="false">

                            @if (Auth::user()->user_role === 'admin')
                                <li class="nav-item ">
                                    <a href="{{ route('nbfc_dashboard') }}" class="nav-link">
                                        <div class="d-flex">
                                            <div class="nav-text">
                                                <i class="fa fa-list"></i>
                                                <p>All NBFC Dashboard</p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endif


                            <li class="nav-item ">
                                <a href="{{ route('dashboard') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-list"></i>
                                            <p>Dashboard</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('disbursement.create') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-plus"></i>
                                            <p>Upload Disbursement CSV</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('collection.create') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-plus"></i>
                                            <p>Upload Collection CSV</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <!--             <li class="nav-item">
                                <a href="{{ route('createcsv') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-plus"></i>
                                            <p>Excel to CSV Convert/Download</p>
                                        </div>
                                    </div>
                                </a>
   </li>
                 -->

                            <li class="nav-item">
                                <a href="{{ route('createcsv') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-plus"></i>
                                            <p>Excel to CSV Convert/Download</p>
                                        </div>
                                    </div>
                                </a>
                            </li>


                            <li class="nav-item ">
                                <a href="{{ route('loan_account.index') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-list"></i>
                                            <p>Loan Accounts</p>
                                        </div>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('disbursebatch.index') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-list"></i>
                                            <p>Disbursement Batches</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('batch.index') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-list"></i>
                                            <p>Collection Batches</p>
                                        </div>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('classification', 'SMA0') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-list"></i>
                                            <p>SMA0</p>
                                        </div>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('classification', 'SMA1') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-list"></i>
                                            <p>SMA1</p>
                                        </div>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('classification', 'SMA2') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-list"></i>
                                            <p>SMA2</p>
                                        </div>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('classification', 'NPA') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-list"></i>
                                            <p>NPA</p>
                                        </div>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('closed') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-list"></i>
                                            <p>Closed Account</p>
                                        </div>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('setting.create') }}" class="nav-link">
                                    <div class="d-flex">
                                        <div class="nav-text">
                                            <i class="fa fa-edit"></i>
                                            <p>Update Interest and Gold Rate</p>
                                        </div>
                                    </div>
                                </a>
                            </li>

                        </ul>
                    </nav>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>
            <div class="content-wrapper">
                <!-- Main content -->
                <section class="content">

                    <div class="container-fluid">
                        <!-- Main row -->
                        <div class="row">
                            <!-- Left col -->
                            <section class="col-lg-12 connectedSortable">
                                <!-- Custom tabs (Charts with tabs)-->
                                @yield('content')
                                <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                </section>
                <!-- /.Left col -->
            </div>
            <!-- /.row (main row) -->
        </div>
        <!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>
    <!-- /.content-wrapper -->
    </div>
    </div>
    <script src="{{ env('BASE') . '/all-files/plugins/jquery/jquery.min.js' }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ env('BASE') . '/all-files/plugins/jquery-ui/jquery-ui.min.js' }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ env('BASE') . '/all-files/plugins/bootstrap/js/bootstrap.bundle.min.js' }}"></script>
    <script src="{{ env('BASE') . '/all-files/dist/js/adminlte.js' }}"></script>
    @yield('script')
</body>

</html>
