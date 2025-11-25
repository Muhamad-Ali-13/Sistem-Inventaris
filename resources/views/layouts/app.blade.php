<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory System</title>

    {{-- <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
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
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <i class="fas fa-edit mr-2"></i> Edit Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                @endauth
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @auth
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="{{ route('dashboard') }}" class="brand-link">
                    <span class="brand-text font-weight-light">Inventory System</span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="info">
                            <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                        </div>
                    </div>

                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                            data-accordion="false">
                            <li class="nav-item">
                                <a href="{{ route('dashboard') }}" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>

                            @if (auth()->check() &&
                                    auth()->user()->hasRole(['super admin', 'admin']))
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-building"></i>
                                        <p>
                                            Management
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('departments.index') }}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Departments</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('employees.index') }}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Employees</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('categories.index') }}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Categories</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('items.index') }}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Items</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('vehicles.index') }}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Vehicles</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('users.index') }}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Users</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-exchange-alt"></i>
                                        <p>
                                            Transactions
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('transactions.index') }}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Transactions</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('item-requests.index') }}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Item Requests</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('vehicle-usage.index') }}" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Vehicle Usage</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('reports.index') }}" class="nav-link">
                                        <i class="nav-icon fas fa-chart-bar"></i>
                                        <p>Reports</p>
                                    </a>
                                </li>
                            @endif

                            @if (auth()->check() && auth()->user()->hasRole('super admin'))
                                <li class="nav-item">
                                    <a href="{{ route('roles.index') }}" class="nav-link">
                                        <i class="nav-icon fas fa-user-lock"></i>
                                        <p>Roles & Permissions</p>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item">
                                <a href="{{ route('item-requests.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>My Item Requests</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('vehicle-usage.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-car"></i>
                                    <p>My Vehicle Usage</p>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>
        @endauth

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2024 Inventory System.</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    @stack('scripts')
</body>

</html>
