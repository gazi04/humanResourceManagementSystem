<!-- resources/views/layouts/layout.blade.php -->

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
    <title>@yield('title', 'K.R.U Hidromorava')</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/line-awesome.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />


</head>

<body>
    <div class="main-wrapper">

        @include('admin.includes.header')

        @include('admin.includes.sidebar')

        <div class="page-wrapper">
            @yield('title', 'K.R.U Hidromorava')yield('content')
        </div>
    </div>

</body>

</html>