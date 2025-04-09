<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <meta name="description" content="Smarthr - Bootstrap Admin Template" />
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects" />
    <meta name="author" content="Dreamguys - Bootstrap Admin Template" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Tickets - HRMS admin template</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}" />

    <!-- Lineawesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/line-awesome.min.css') }}" />

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}" />

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="{{ asset('assets/js/html5shiv.min.js') }}"></script>
      <script src="{{ asset('assets/js/respond.min.js') }}"></script>
    <![endif]-->
</head>

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Header -->
        <div class="header">
            <!-- Logo -->
            <div class="header-left">
                <a href="index.html" class="logo">
                    <img src="assets/img/logo.png" width="40" height="40" alt="" />
                </a>
            </div>
            <!-- /Logo -->

            <a id="toggle_btn" href="javascript:void(0);">
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>

            <!-- Header Title -->
            <div class="page-title-box">
                <h3>K.R.U Hidromorava</h3>
            </div>
            <!-- /Header Title -->

            <a id="mobile_btn" class="mobile_btn" href="#sidebar"><i class="fa fa-bars"></i></a>

            <!-- Header Menu -->
            <ul class="nav user-menu">
                <!-- Search -->
                <li class="nav-item">
                    <div class="top-nav-search">
                        <a href="javascript:void(0);" class="responsive-search">
                            <i class="fa fa-search"></i>
                        </a>
                        <form action="search.html">
                            <input class="form-control" type="text" placeholder="Kerko" />
                            <button class="btn" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>
                </li>
                <!-- /Search -->

                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="badge badge-pill">3</span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span class="notification-title">Notifications</span>
                            <a href="javascript:void(0)" class="clear-noti"> Clear All </a>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">
                                <li class="notification-message">
                                    <a href="activities.html">
                                        <div class="media">
                                            <span class="avatar">
                                                <img alt="" src="assets/img/profiles/avatar-02.jpg" />
                                            </span>
                                            <div class="media-body">
                                                <p class="noti-details">
                                                    <span class="noti-title">John Doe</span> added new
                                                    task
                                                    <span class="noti-title">Patient appointment booking</span>
                                                </p>
                                                <p class="noti-time">
                                                    <span class="notification-time">4 mins ago</span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="activities.html">
                                        <div class="media">
                                            <span class="avatar">
                                                <img alt="" src="assets/img/profiles/avatar-03.jpg" />
                                            </span>
                                            <div class="media-body">
                                                <p class="noti-details">
                                                    <span class="noti-title">Tarah Shropshire</span>
                                                    changed the task name
                                                    <span class="noti-title">Appointment booking with payment
                                                        gateway</span>
                                                </p>
                                                <p class="noti-time">
                                                    <span class="notification-time">6 mins ago</span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="activities.html">
                                        <div class="media">
                                            <span class="avatar">
                                                <img alt="" src="assets/img/profiles/avatar-06.jpg" />
                                            </span>
                                            <div class="media-body">
                                                <p class="noti-details">
                                                    <span class="noti-title">Misty Tison</span> added
                                                    <span class="noti-title">Domenic Houston</span> and
                                                    <span class="noti-title">Claire Mapes</span> to
                                                    project
                                                    <span class="noti-title">Doctor available module</span>
                                                </p>
                                                <p class="noti-time">
                                                    <span class="notification-time">8 mins ago</span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="activities.html">
                                        <div class="media">
                                            <span class="avatar">
                                                <img alt="" src="assets/img/profiles/avatar-17.jpg" />
                                            </span>
                                            <div class="media-body">
                                                <p class="noti-details">
                                                    <span class="noti-title">Rolland Webber</span>
                                                    completed task
                                                    <span class="noti-title">Patient and Doctor video
                                                        conferencing</span>
                                                </p>
                                                <p class="noti-time">
                                                    <span class="notification-time">12 mins ago</span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="activities.html">
                                        <div class="media">
                                            <span class="avatar">
                                                <img alt="" src="assets/img/profiles/avatar-13.jpg" />
                                            </span>
                                            <div class="media-body">
                                                <p class="noti-details">
                                                    <span class="noti-title">Bernardo Galaviz</span>
                                                    added new task
                                                    <span class="noti-title">Private chat module</span>
                                                </p>
                                                <p class="noti-time">
                                                    <span class="notification-time">2 days ago</span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="activities.html">View all Notifications</a>
                        </div>
                    </div>
                </li>
                <!-- /Notifications -->

                <li class="nav-item dropdown has-arrow main-drop">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <span class="user-img"><img src="assets/img/profiles/avatar-21.jpg" alt="" />
                            <span class="status online"></span></span>
                        <span>Admin</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile.html">My Profile</a>
                        <a class="dropdown-item" href="settings.html">Settings</a>
                        <a class="dropdown-item" href="login.html">Logout</a>
                    </div>
                </li>
            </ul>
            <!-- /Header Menu -->

            <!-- Mobile Menu -->
            <div class="dropdown mobile-user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i
                        class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.html">My Profile</a>
                    <a class="dropdown-item" href="settings.html">Settings</a>
                    <a class="dropdown-item" href="login.html">Logout</a>
                </div>
            </div>
            <!-- /Mobile Menu -->
        </div>
        <!-- /Header -->

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">
                            <span>Main</span>
                        </li>
                        <li class="submenu">
                            <a href="#">Paneli i Adminit</a>
                        </li>

                        <li class="submenu">
                            <a href="#"><i class="la la-user"></i> <span> Puntoret</span>
                                <span class="menu-arrow"></span></a>
                            <ul style="display: none">
                                <li><a href="admin.html">Administratoret</a></li>
                                <li><a href="hrEmploye.html">Hr</a></li>
                                <li>
                                    <a href="depMenager.html">Menaxheri i Departamenteve</a>
                                </li>
                                <li><a href="employee.html">Puntoret</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="departments.html"><i class="la la-users"></i> <span>Departamentet</span></a>
                        </li>

                        <li>
                            <a href="tickets.html"><i class="la la-ticket"></i> <span>Tickets</span></a>
                        </li>

                        <li class="submenu">
                            <a href="#"><i class="la la-pie-chart"></i> <span> Reports </span>
                                <span class="menu-arrow"></span></a>
                            <ul style="display: none">
                                <li><a href="expense-reports.html"> Expense Report </a></li>
                                <li><a href="invoice-reports.html"> Invoice Report </a></li>
                            </ul>
                        </li>
                    </ul>
                    <li>
                        <a href="settings.html"><i class="la la-ticket"></i> <span>Settings</span></a>
                    </li>
                </div>
            </div>
        </div>
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Page Content -->
            <div class="content container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Tickets</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('admin') }}">Paneli i admin</a>
                                </li>
                                <li class="breadcrumb-item active">Tickets</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Success Message (from first template) -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Stats Cards (from second template) -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-group m-b-30">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="d-block">Tickets tani</span>
                                        </div>
                                    </div>
                                    <h3 class="mb-3">112</h3>
                                    <div class="progress mb-2" style="height: 5px">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 70%"
                                            aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="d-block">Tickets të kryera</span>
                                        </div>
                                    </div>
                                    <h3 class="mb-3">70</h3>
                                    <div class="progress mb-2" style="height: 5px">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 70%"
                                            aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="d-block">Tickets në pritje</span>
                                        </div>
                                        <div></div>
                                    </div>
                                    <h3 class="mb-3">100</h3>
                                    <div class="progress mb-2" style="height: 5px">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 70%"
                                            aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Filter (from second template) -->
                <div class="row filter-row">
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 col-12">
                        <div class="form-group form-focus select-focus">
                            <select class="select floating">
                                <option>-- Zgjedh --</option>
                                <option>Të reja</option>
                                <option>Në pritje</option>
                                <option>Të kryera</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
                        <a href="#" class="btn btn-success btn-block"> Kërko </a>
                    </div>
                </div>
                <!-- /Search Filter -->

                <!-- Ticket Table (from first template, styled to match second template) -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Requester</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $ticket->description }}">
                                                    {{ $ticket->description }}
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $ticket->status === 'closed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($ticket->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $ticket->firstname }} {{ $ticket->lastname }}</td>
                                            <td class="text-right">
                                                <button class="btn btn-primary" data-toggle="modal"
                                                    data-target="#delete_ticket">
                                                    Kryej
                                                </button>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No tickets found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>



                    </div>
                </div>
            </div>
            <!-- /Page Content -->

            <!-- Delete Ticket Modal (from second template) -->
            <div class="modal custom-modal fade" id="delete_ticket" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="form-header">
                                <h3>Kryej Ticketen</h3>
                                <p>A jeni i sigurte qe doni ta kryeni?</p>
                            </div>
                            <div class="modal-btn delete-action">
                                <div class="row">
                                    <div class="col-6">
                                        {{--TODO- DIMAL QITU PER ME KRY NJE TICKET NEVOJITET ID TICKETS SI ME FORM E NDREQ EDHE ID E TICKETS E VENDOS ME NJE HIDDEN INPUT--}}
                                        {{--HINT- DIMAL ROUTE PER ME KRY NJE TICKET ESHTE ADMIN.TICKET.FINISH--}}
                                        <a href="javascript:void(0);" class="btn btn-primary continue-btn">Delete</a>
                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0);" data-dismiss="modal"
                                            class="btn btn-primary cancel-btn">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Delete Ticket Modal -->
        </div>
        <!-- /Page Wrapper -->
        <!-- jQuery -->
        <script src="{{ asset('assets/js/jquery-3.2.1.min.js') }}"></script>

        <!-- Bootstrap Core JS -->
        <script src="{{ asset('assets/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

        <!-- Slimscroll JS -->
        <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>

        <!-- Select2 JS -->
        <script src="{{ asset('assets/js/select2.min.js') }}"></script>

        <!-- Datetimepicker JS -->
        <script src="{{ asset('assets/js/moment.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>

        <!-- Datatable JS -->
        <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>

        <!-- Custom JS -->
        <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>
