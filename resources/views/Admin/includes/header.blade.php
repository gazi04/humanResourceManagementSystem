<!-- resources/views/layouts/header.blade.php -->

<!-- Header -->
<div class="header">
    <!-- User Dropdown -->
    <ul class="nav user-menu">
        <li class="nav-item dropdown has-arrow main-drop">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                <span class="user-img">
                    <img src="{{ Auth::user()->profile_picture }}" alt="" />
                    <span class="status online"></span>
                </span>
                <span>{{ Auth::user()->name }}</span>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('profile') }}">Profili</a>
                <a class="dropdown-item" href="{{ route('settings') }}">Settings</a>
                <a class="dropdown-item" href="{{ route('logout') }}">Ckycu</a>
            </div>
        </li>
    </ul>
</div>
<!-- /Header -->