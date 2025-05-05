<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 1) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Header -->
    <?php include 'layouts/head.php'; ?>

    <!-- Custom CSS -->
    <link href="../assets/css/admindash.css" rel="stylesheet">
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'layouts/sidebar.php'; ?>

            <!-- Main Content -->
            <main id="content" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <i class="bi bi-calendar"></i> This week
                        </button>
                    </div>
                </div>

                <!-- Dashboard Content -->
                <div class="row mb-4">
                    <!-- Stat Cards -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow h-100 py-2"
                            style="border-left-color: #4e73df !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Revenue (Monthly)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">$40,000</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow h-100 py-2"
                            style="border-left-color: #1cc88a !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Species</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">1,248</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-fish fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow h-100 py-2"
                            style="border-left-color: #36b9cc !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasks
                                        </div>
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-auto">
                                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">80%</div>
                                            </div>
                                            <div class="col">
                                                <div class="progress progress-sm mr-2">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: 80%" aria-valuenow="80" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-clipboard-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow h-100 py-2"
                            style="border-left-color: #f6c23e !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pending Requests</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical text-gray-400"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                                        <li><a class="dropdown-item" href="#">Action</a></li>
                                        <li><a class="dropdown-item" href="#">Another action</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-area">
                                    <div
                                        style="height: 300px; background-color: #f8f9fc; display: flex; align-items: center; justify-content: center;">
                                        <p class="text-center text-muted">Chart Area (Line Chart)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink2"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical text-gray-400"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink2">
                                        <li><a class="dropdown-item" href="#">Action</a></li>
                                        <li><a class="dropdown-item" href="#">Another action</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-pie">
                                    <div
                                        style="height: 300px; background-color: #f8f9fc; display: flex; align-items: center; justify-content: center;">
                                        <p class="text-center text-muted">Chart Area (Pie Chart)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity & Latest Transactions -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">New user registered</h6>
                                            <small>3 minutes ago</small>
                                        </div>
                                        <p class="mb-1">John Doe created an account.</p>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">System update completed</h6>
                                            <small>1 hour ago</small>
                                        </div>
                                        <p class="mb-1">Version 2.3.4 successfully deployed.</p>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Database backup</h6>
                                            <small>2 hours ago</small>
                                        </div>
                                        <p class="mb-1">Database backup completed successfully.</p>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">New order placed</h6>
                                            <small>Yesterday</small>
                                        </div>
                                        <p class="mb-1">Order #45782 has been placed.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Latest Transactions</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>#45782</td>
                                                <td>John Smith</td>
                                                <td>$242.00</td>
                                                <td><span class="badge bg-success">Completed</span></td>
                                            </tr>
                                            <tr>
                                                <td>#45781</td>
                                                <td>Alice Johnson</td>
                                                <td>$125.50</td>
                                                <td><span class="badge bg-warning text-dark">Pending</span></td>
                                            </tr>
                                            <tr>
                                                <td>#45780</td>
                                                <td>Robert Brown</td>
                                                <td>$542.00</td>
                                                <td><span class="badge bg-success">Completed</span></td>
                                            </tr>
                                            <tr>
                                                <td>#45779</td>
                                                <td>Emily Davis</td>
                                                <td>$151.25</td>
                                                <td><span class="badge bg-danger">Failed</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <?php include 'layouts/footer.php'; ?>
            </main>
        </div>
    </div>

    <!-- Dark Overlay for Mobile Sidebar -->
    <div class="overlay"></div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle sidebar on button click
            document.getElementById('sidebarCollapse').addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('active');
                document.querySelector('.overlay').classList.toggle('active');
            });

            // Close sidebar when clicking on overlay
            document.querySelector('.overlay').addEventListener('click', function () {
                document.getElementById('sidebar').classList.remove('active');
                this.classList.remove('active');
            });
        });
    </script>
</body>

</html>