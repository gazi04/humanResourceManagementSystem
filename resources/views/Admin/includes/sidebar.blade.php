<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main</span>
                </li>

                <li class="submenu">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="la la-dashboard"></i> <span> Paneli i Admin</span>
                        <span class="menu-arrow"></span>
                    </a>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="la la-user"></i> <span> Puntoret</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none">
                        <li><a href="{{ route('admin.employee.administrators') }}">Administratoret</a></li>
                        <li><a href="{{ route('admin.employee.hrs') }}">Hr</a></li>
                        <li><a href="{{ route('admin.employee.managers') }}">Menaxheri i Departamenteve</a></li>
                        <li><a href="{{ route('admin.employee.index') }}">Puntoret</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('admin.department.index') }}">
                        <i class="la la-users"></i> <span>Departamentet</span>
                    </a>
                </li>

                <li>

                    <a href="{{ route('admin.ticket.index') }}">

                        <i class="la la-ticket"></i> <span>Tickets</span>
                    </a>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="la la-pie-chart"></i> <span> Reports </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none">
                        <li><a href="#">Expense Report</a></li>
                        <li><a href="#">Invoice Report</a></li>
                        {{-- Define these routes if needed --}}
                    </ul>
                </li>

                <li>
                    <a href="{{ route('logout') }}">
                        <i class="la la-cog"></i> <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>