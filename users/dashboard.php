<?php
session_start(); // Ensure the session is started

// Check if UID is set in the session
if (!isset($_SESSION['UID'])) {
    header("Location: ../login.php"); // Redirect to login if UID is not set
    exit();
}

include '../assets/db.php';
include 'modal/dashboard_backend.php';

// Fetch total catches
$totalCatchesQuery = "SELECT SUM(quantity) AS total_catches FROM catch_reports";
$totalCatchesResult = $conn->query($totalCatchesQuery);
$totalCatches = $totalCatchesResult ? $totalCatchesResult->fetch_assoc()['total_catches'] ?? 0 : 0;

// Fetch active species
$activeSpeciesQuery = "SELECT COUNT(*) AS active_species FROM species WHERE is_available = 1";
$activeSpeciesResult = $conn->query($activeSpeciesQuery);
$activeSpecies = $activeSpeciesResult ? $activeSpeciesResult->fetch_assoc()['active_species'] ?? 0 : 0;

// Fetch total violations
$violationsCountQuery = "SELECT COUNT(*) AS total_violations FROM violations";
$violationsCountResult = $conn->query($violationsCountQuery);
$totalViolations = $violationsCountResult ? $violationsCountResult->fetch_assoc()['total_violations'] ?? 0 : 0;

// Fetch endangered species
$endangeredSpeciesQuery = "SELECT COUNT(*) AS endangered_species FROM species WHERE endangered_level = 'High'";
$endangeredSpeciesResult = $conn->query($endangeredSpeciesQuery);
$endangeredSpecies = $endangeredSpeciesResult ? $endangeredSpeciesResult->fetch_assoc()['endangered_species'] ?? 0 : 0;

// Fetch user-specific total catches
$UID = $_SESSION['UID'];
$userCatchesQuery = "SELECT SUM(quantity) AS user_catches FROM catch_reports WHERE UID = $UID";
$userCatchesResult = $conn->query($userCatchesQuery);
$userCatches = $userCatchesResult ? $userCatchesResult->fetch_assoc()['user_catches'] ?? 0 : 0;

// Fetch user-specific violations
$userViolationsQuery = "SELECT COUNT(*) AS user_violations FROM violations WHERE UID = $UID";
$userViolationsResult = $conn->query($userViolationsQuery);
$userViolations = $userViolationsResult ? $userViolationsResult->fetch_assoc()['user_violations'] ?? 0 : 0;

// Fetch data for charts
$catchesPerSpecies = $conn->query("SELECT species.name, SUM(catch_reports.quantity) AS count 
                                   FROM catch_reports 
                                   JOIN species ON catch_reports.SID = species.SID 
                                   GROUP BY catch_reports.SID");

$endangeredLevels = $conn->query("SELECT endangered_level, COUNT(*) AS count 
                                  FROM species 
                                  GROUP BY endangered_level");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("layouts/head.php"); ?>
</head>

<body style="background-color: #e8f4fc;">
    <?php include("layouts/navbar.php"); ?>

    <main class="container my-4" style="background-color: #e8f4fc;">
        <div class="row g-4">
            <!-- Stat Cards -->
            <div class="col-lg-3 col-md-6">
                <div class="card text-center shadow-sm stat-card-hover">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-fish me-2"></i>My Total Catches</h5>
                        <p class="card-text fs-4 fw-bold"><?php echo $userCatches; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center shadow-sm stat-card-hover">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-water me-2"></i>Active Species</h5>
                        <p class="card-text fs-4 fw-bold"><?php echo $activeSpecies; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center shadow-sm stat-card-hover">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-exclamation-triangle me-2"></i>My Violations</h5>
                        <p class="card-text fs-4 fw-bold"><?php echo $userViolations; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center shadow-sm stat-card-hover">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-skull-crossbones me-2"></i>Endangered Species</h5>
                        <p class="card-text fs-4 fw-bold"><?php echo $endangeredSpecies; ?></p>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Catches Per Species</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="catchesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Species by Endangered Levels</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center">
                        <div style="width: 400px; height: 300px;">
                            <canvas id="endangeredChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Catch Reports -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Catch Reports</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php
                            $recentCatches = $conn->query("SELECT species.name AS species_name, quantity, catch_date 
                                                           FROM catch_reports 
                                                           JOIN species ON catch_reports.SID = species.SID 
                                                           ORDER BY catch_date DESC LIMIT 3");
                            while ($row = $recentCatches->fetch_assoc()): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?= htmlspecialchars($row['species_name']) ?> - <?= $row['quantity'] ?>
                                        Catches</span>
                                    <small class="text-muted"><?= htmlspecialchars($row['catch_date']) ?></small>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Violations -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Violations</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php
                            $recentViolations = $conn->query("SELECT description, penalty, date 
                                                             FROM violations 
                                                             ORDER BY date DESC LIMIT 3");
                            while ($row = $recentViolations->fetch_assoc()): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?= htmlspecialchars($row['description']) ?></span>
                                    <small class="text-danger">Penalty: ₱<?= number_format($row['penalty'], 2) ?></small>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include("layouts/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Catches Per Species Chart
        const ctxCatches = document.getElementById('catchesChart').getContext('2d');
        const catchesData = {
            labels: [<?php while ($row = $catchesPerSpecies->fetch_assoc())
                echo "'{$row['name']}',"; ?>],
            datasets: [{
                label: 'Catches',
                data: [<?php $catchesPerSpecies->data_seek(0);
                while ($row = $catchesPerSpecies->fetch_assoc())
                    echo "{$row['count']},"; ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };
        new Chart(ctxCatches, { type: 'bar', data: catchesData });

        const ctxEndangered = document.getElementById('endangeredChart').getContext('2d');
        const endangeredData = {
            labels: [<?php while ($row = $endangeredLevels->fetch_assoc())
                echo "'{$row['endangered_level']}',"; ?>],
            datasets: [{
                label: 'Species Count',
                data: [<?php $endangeredLevels->data_seek(0);
                while ($row = $endangeredLevels->fetch_assoc())
                    echo "{$row['count']},"; ?>],
                backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(255, 99, 132, 0.5)'],
                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        };
        new Chart(ctxEndangered, { type: 'pie', data: endangeredData });
    </script>

    <style>
        .stat-card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        #endangeredChart {
            max-width: 100%;
            max-height: 100%;
        }
    </style>
</body>

</html>