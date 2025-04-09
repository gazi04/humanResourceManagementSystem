<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li class="submenu">
                    <a href="{{ url('admin') }}"><i class=" la la-dashboard"></i> <span> Paneli i Admin</span>
                        <span class="menu-arrow"></span></a>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="la la-user"></i> <span> Puntoret</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none">
                        <li><a href="{{ url('admin') }}">Administratoret</a></li>
                        <li><a href="{{ url('hrEmploye') }}">Hr</a></li>
                        <li><a href="{{ url('depMenager') }}">Menaxheri i Departamenteve</a></li>
                        <li><a href="{{ url('employee') }}">Puntoret</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ url('departments') }}">
                        <i class="la la-users"></i> <span>Departamentet</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('tickets') }}">
                        <i class="la la-ticket"></i> <span>Tickets</span>
                    </a>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="la la-pie-chart"></i> <span> Reports </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none">
                        <li><a href="{{ url('expense-reports') }}">Expense Report</a></li>
                        <li><a href="{{ url('invoice-reports') }}">Invoice Report</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ url('settings') }}">
                        <i class="la la-cog"></i> <span>Settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
