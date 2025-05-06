<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 1) {
    header("Location: ../index.php");
    exit();
}

// Database connection
include '../assets/db.php';

// Fetch most caught species
$mostCaughtSpeciesQuery = "SELECT s.name, SUM(cr.quantity) AS total_quantity 
                           FROM catch_reports cr 
                           JOIN species s ON cr.SID = s.SID 
                           GROUP BY cr.SID 
                           ORDER BY total_quantity DESC 
                           LIMIT 1";
$mostCaughtSpeciesResult = $conn->query($mostCaughtSpeciesQuery);
$mostCaughtSpecies = $mostCaughtSpeciesResult->fetch_assoc();

// Fetch user with most violations
$mostViolationsQuery = "SELECT u.name, COUNT(v.id) AS total_violations 
                        FROM violations v 
                        JOIN users u ON v.UID = u.UID 
                        GROUP BY v.UID 
                        ORDER BY total_violations DESC 
                        LIMIT 1";
$mostViolationsResult = $conn->query($mostViolationsQuery);
$mostViolationsUser = $mostViolationsResult->fetch_assoc();

// Fetch endangered species with high endangered levels
$endangeredSpeciesQuery = "SELECT s.name, s.endangered_level 
                           FROM species s 
                           WHERE s.endangered_level = 'High' 
                           ORDER BY s.name ASC";
$endangeredSpeciesResult = $conn->query($endangeredSpeciesQuery);

// Fetch data for charts
$speciesChartQuery = "SELECT s.name, SUM(cr.quantity) AS total_quantity 
                      FROM catch_reports cr 
                      JOIN species s ON cr.SID = s.SID 
                      GROUP BY cr.SID 
                      ORDER BY total_quantity DESC";
$speciesChartResult = $conn->query($speciesChartQuery);
$speciesChartData = [];
while ($row = $speciesChartResult->fetch_assoc()) {
    $speciesChartData[] = $row;
}

// Fetch data for resolved and unresolved violations
$violationsStatusQuery = "SELECT 
                            CASE 
                                WHEN resolved = 1 THEN 'Resolved' 
                                ELSE 'Unresolved' 
                            END AS status, 
                            COUNT(id) AS total 
                          FROM violations 
                          GROUP BY status";
$violationsStatusResult = $conn->query($violationsStatusQuery);
$violationsStatusData = [];
while ($row = $violationsStatusResult->fetch_assoc()) {
    $violationsStatusData[] = $row;
}

// Fetch data for catch trends over time with formatted dates
$catchTrendsQuery = "SELECT DATE_FORMAT(catch_date, '%d-%b-%Y') AS formatted_date, SUM(quantity) AS total_quantity 
                     FROM catch_reports 
                     GROUP BY catch_date 
                     ORDER BY catch_date ASC";
$catchTrendsResult = $conn->query($catchTrendsQuery);
$catchTrendsData = [];
while ($row = $catchTrendsResult->fetch_assoc()) {
    $catchTrendsData[] = $row;
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
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Fishguard Analytics</h1>
                </div>

                <div class="row mb-4">
                    <!-- Most Caught Species -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow h-100 py-2" style="border-left-color: #4e73df !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            <i class="bi bi-fish"></i> Most Caught Species
                                        </div>
                                        <?php if ($mostCaughtSpecies): ?>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= htmlspecialchars($mostCaughtSpecies['name']) ?>
                                            </div>
                                            <div class="text-muted">Total Quantity: <?= htmlspecialchars($mostCaughtSpecies['total_quantity']) ?></div>
                                        <?php else: ?>
                                            <div class="text-muted">No data available.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fisherman with Most Violations -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow h-100 py-2" style="border-left-color: #e74a3b !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            <i class="bi bi-person-badge"></i> Fisherman with Most Violations
                                        </div>
                                        <?php if ($mostViolationsUser): ?>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= htmlspecialchars($mostViolationsUser['name']) ?>
                                            </div>
                                            <div class="text-muted">Total Violations: <?= htmlspecialchars($mostViolationsUser['total_violations']) ?></div>
                                        <?php else: ?>
                                            <div class="text-muted">No data available.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Endangered Species with High Endangered Levels -->
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card stat-card border-0 shadow h-100 py-2" style="border-left-color: #f6c23e !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            <i class="bi bi-exclamation-triangle"></i> Endangered Species with High Levels
                                        </div>
                                        <?php if ($endangeredSpeciesResult->num_rows > 0): ?>
                                            <ul class="mb-0">
                                                <?php while ($row = $endangeredSpeciesResult->fetch_assoc()): ?>
                                                    <li>
                                                        <strong><?= htmlspecialchars($row['name']) ?></strong>: 
                                                        <?= htmlspecialchars($row['endangered_level']) ?> level
                                                    </li>
                                                <?php endwhile; ?>
                                            </ul>
                                        <?php else: ?>
                                            <div class="text-muted">No data available.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Most Caught Species Chart -->
                    <div class="col-md-6">
                        <div class="card stat-card border-0 shadow h-100 py-2" style="border-left-color: #4e73df !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Most Caught Species
                                        </div>
                                        <canvas id="speciesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Violations Status Chart -->
                    <div class="col-md-6">
                        <div class="card stat-card border-0 shadow h-100 py-2" style="border-left-color: #e74a3b !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Violations Status
                                        </div>
                                        <canvas id="violationsChart" style="max-width: 300px; max-height: 300px; margin: auto;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4 mb-4">
                    <!-- Catch Trends Chart -->
                    <div class="col-md-12">
                        <div class="card stat-card border-0 shadow h-100 py-2" style="border-left-color: #36b9cc !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Catch Trends Over Time
                                        </div>
                                        <canvas id="catchTrendsChart"></canvas>
                                    </div>
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

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle sidebar on button click
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            if (sidebarCollapse) {
                sidebarCollapse.addEventListener('click', function () {
                    document.getElementById('sidebar').classList.toggle('active');
                    document.querySelector('.overlay').classList.toggle('active');
                });
            }

            // Close sidebar when clicking on overlay
            const overlay = document.querySelector('.overlay');
            if (overlay) {
                overlay.addEventListener('click', function () {
                    document.getElementById('sidebar').classList.remove('active');
                    this.classList.remove('active');
                });
            }

            // Species Chart Data
            const speciesLabels = <?= json_encode(array_column($speciesChartData, 'name')) ?>;
            const speciesData = <?= json_encode(array_column($speciesChartData, 'total_quantity')) ?>;

            // Violations Status Chart Data
            const violationsStatusLabels = <?= json_encode(array_column($violationsStatusData, 'status')) ?>;
            const violationsStatusData = <?= json_encode(array_column($violationsStatusData, 'total')) ?>;

            // Render Species Chart
            const speciesCtx = document.getElementById('speciesChart').getContext('2d');
            new Chart(speciesCtx, {
                type: 'bar',
                data: {
                    labels: speciesLabels,
                    datasets: [{
                        label: 'Total Quantity Caught',
                        data: speciesData,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true },
                        title: { display: true, text: 'Most Caught Species' }
                    }
                }
            });

            // Render Violations Status Chart
            const violationsCtx = document.getElementById('violationsChart').getContext('2d');
            new Chart(violationsCtx, {
                type: 'pie',
                data: {
                    labels: violationsStatusLabels,
                    datasets: [{
                        label: 'Violations Status',
                        data: violationsStatusData,
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)', // Resolved
                            'rgba(255, 99, 132, 0.2)'  // Unresolved
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)', // Resolved
                            'rgba(255, 99, 132, 1)'  // Unresolved
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true },
                        title: { display: true, text: 'Violations Status' }
                    }
                }
            });

            // Catch Trends Chart Data
            const catchTrendsLabels = <?= json_encode(array_column($catchTrendsData, 'formatted_date')) ?>;
            const catchTrendsData = <?= json_encode(array_column($catchTrendsData, 'total_quantity')) ?>;

            // Render Catch Trends Chart
            const catchTrendsCtx = document.getElementById('catchTrendsChart').getContext('2d');
            new Chart(catchTrendsCtx, {
                type: 'line',
                data: {
                    labels: catchTrendsLabels,
                    datasets: [{
                        label: 'Total Quantity Caught',
                        data: catchTrendsData,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4 // Add tension for smooth curves
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true },
                        title: { display: true, text: 'Catch Trends Over Time' }
                    },
                    scales: {
                        x: { title: { display: true, text: 'Date' } },
                        y: { title: { display: true, text: 'Quantity' } }
                    }
                }
            });
        });
    </script>
</body>

</html>