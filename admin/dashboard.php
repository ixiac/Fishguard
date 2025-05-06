<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 1) {
    header("Location: ../index.php");
    exit();
}

// Database connection
include '../assets/db.php';

// Fetch total users
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];

// Fetch total species
$totalSpecies = $conn->query("SELECT COUNT(*) AS count FROM species")->fetch_assoc()['count'];

// Fetch total catch reports
$totalCatchReports = $conn->query("SELECT COUNT(*) AS count FROM catch_reports")->fetch_assoc()['count'];

// Fetch total violations (pending and resolved)
$totalViolations = $conn->query("SELECT COUNT(*) AS count FROM violations")->fetch_assoc()['count'];
$pendingViolations = $conn->query("SELECT COUNT(*) AS count FROM violations WHERE resolved = 0")->fetch_assoc()['count'];

// Fetch data for charts
$catchesPerSpecies = $conn->query("SELECT species.name, COUNT(catch_reports.SID) AS count 
                                   FROM catch_reports 
                                   JOIN species ON catch_reports.SID = species.SID 
                                   GROUP BY catch_reports.SID");

$endangeredLevels = $conn->query("SELECT endangered_level, COUNT(*) AS count 
                                  FROM species 
                                  GROUP BY endangered_level");

// Fetch data for violations per user
$violationsPerUser = $conn->query("SELECT users.name AS user_name, COUNT(violations.UID) AS count 
                                   FROM violations 
                                   JOIN users ON violations.UID = users.UID 
                                   GROUP BY violations.UID");
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
                    <h1 class="h2">Admin Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                    </div>
                </div>

                <!-- Dynamic Stat Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow h-100 py-2" style="border-left-color: #4e73df !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalUsers; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow h-100 py-2" style="border-left-color: #1cc88a !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Species</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalSpecies; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-water fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow h-100 py-2" style="border-left-color: #36b9cc !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Catch Reports
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalCatchReports; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-clipboard-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow h-100 py-2" style="border-left-color: #f6c23e !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pending Violations</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pendingViolations; ?></div>
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
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Catches Per Species</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="catchesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Fishermen Violations</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="violationsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity & Latest Transactions -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Catch Reports</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>User</th>
                                            <th>Species</th>
                                            <th>Quantity</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $recentCatches = $conn->query("SELECT users.name AS user_name, species.name AS species_name, quantity, catch_date 
                                                                       FROM catch_reports 
                                                                       JOIN users ON catch_reports.UID = users.UID 
                                                                       JOIN species ON catch_reports.SID = species.SID 
                                                                       ORDER BY catch_date DESC LIMIT 5");
                                        while ($row = $recentCatches->fetch_assoc()) {
                                            $formattedDate = date('m/d/Y', strtotime($row['catch_date']));
                                            echo "<tr>
                                                    <td>{$row['user_name']}</td>
                                                    <td>{$row['species_name']}</td>
                                                    <td>{$row['quantity']}</td>
                                                    <td>{$formattedDate}</td>
                                                  </tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Violations</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-hover">
                                    <thead class="table-warning">
                                        <tr>
                                            <th>User</th>
                                            <th>Species</th>
                                            <th>Description</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $recentViolations = $conn->query("SELECT users.name AS user_name, species.name AS species_name, description, date 
                                                                          FROM violations 
                                                                          JOIN users ON violations.UID = users.UID 
                                                                          LEFT JOIN species ON violations.SID = species.SID 
                                                                          ORDER BY date DESC LIMIT 5");
                                        while ($row = $recentViolations->fetch_assoc()) {
                                            $formattedDate = date('m/d/Y', strtotime($row['date']));
                                            echo "<tr>
                                                    <td>{$row['user_name']}</td>
                                                    <td>" . ($row['species_name'] ?? 'N/A') . "</td>
                                                    <td>{$row['description']}</td>
                                                    <td>{$formattedDate}</td>
                                                  </tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
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

    <!-- Charts JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Catches Per Species Chart
        const catchesData = {
            labels: [<?php while ($row = $catchesPerSpecies->fetch_assoc()) echo "'{$row['name']}',"; ?>],
            datasets: [{
                label: 'Catches',
                data: [<?php $catchesPerSpecies->data_seek(0); while ($row = $catchesPerSpecies->fetch_assoc()) echo "{$row['count']},"; ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };
        new Chart(document.getElementById('catchesChart'), { type: 'bar', data: catchesData });

        // Violations Per User Chart
        const violationsData = {
            labels: [<?php while ($row = $violationsPerUser->fetch_assoc()) echo "'{$row['user_name']}',"; ?>],
            datasets: [{
                label: 'Violations',
                data: [<?php $violationsPerUser->data_seek(0); while ($row = $violationsPerUser->fetch_assoc()) echo "{$row['count']},"; ?>],
                backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)'],
                borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)'],
                borderWidth: 1
            }]
        };
        new Chart(document.getElementById('violationsChart'), { type: 'pie', data: violationsData });

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