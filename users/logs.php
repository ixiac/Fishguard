<?php
session_start();

if (!isset($_SESSION['UID'])) {
    header("Location: ../login.php");
    exit();
}

include '../assets/db.php';

// Fetch user data
$uid = $_SESSION['UID'];

// Fetch summary data
$totalCatchReportsQuery = "SELECT COUNT(*) AS total FROM catch_reports WHERE UID = ?";
$stmt = $conn->prepare($totalCatchReportsQuery);
$stmt->bind_param("i", $uid);
$stmt->execute();
$totalCatchReports = $stmt->get_result()->fetch_assoc()['total'];

$totalViolationsQuery = "SELECT COUNT(*) AS total FROM violations WHERE UID = ?";
$stmt = $conn->prepare($totalViolationsQuery);
$stmt->bind_param("i", $uid);
$stmt->execute();
$totalViolations = $stmt->get_result()->fetch_assoc()['total'];

// Fetch total pending penalty
$pendingPenaltyQuery = "SELECT SUM(penalty) AS total_pending_penalty 
                        FROM violations 
                        WHERE UID = ? AND resolved = 0";
$stmt = $conn->prepare($pendingPenaltyQuery);
$stmt->bind_param("i", $uid);
$stmt->execute();
$totalPendingPenalty = $stmt->get_result()->fetch_assoc()['total_pending_penalty'] ?? 0;

// Pagination setup
$itemsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Fetch catch reports
$catchReportsQuery = "SELECT cr.CRID, s.name AS species_name, cr.quantity, cr.size_cm, cr.catch_date 
                      FROM catch_reports cr 
                      JOIN species s ON cr.SID = s.SID 
                      WHERE cr.UID = ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($catchReportsQuery);
$stmt->bind_param("iii", $uid, $itemsPerPage, $offset);
$stmt->execute();
$catchReports = $stmt->get_result();

// Fetch violations
$violationsQuery = "SELECT v.id, s.name AS species_name, v.date, v.description, v.penalty, v.resolved 
                    FROM violations v 
                    LEFT JOIN species s ON v.SID = s.SID 
                    WHERE v.UID = ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($violationsQuery);
$stmt->bind_param("iii", $uid, $itemsPerPage, $offset);
$stmt->execute();
$violations = $stmt->get_result();

// Fetch data for charts
$speciesCatchQuery = "SELECT s.name AS species_name, SUM(cr.quantity) AS total_quantity 
                      FROM catch_reports cr 
                      JOIN species s ON cr.SID = s.SID 
                      WHERE cr.UID = ? 
                      GROUP BY cr.SID";
$stmt = $conn->prepare($speciesCatchQuery);
$stmt->bind_param("i", $uid);
$stmt->execute();
$speciesCatchData = $stmt->get_result();

$violationsResolvedQuery = "SELECT resolved, COUNT(*) AS count 
                            FROM violations 
                            WHERE UID = ? 
                            GROUP BY resolved";
$stmt = $conn->prepare($violationsResolvedQuery);
$stmt->bind_param("i", $uid);
$stmt->execute();
$violationsResolvedData = $stmt->get_result();

// Fetch data for catch trend over time
$catchTrendQuery = "SELECT DATE(cr.catch_date) AS catch_date, SUM(cr.quantity) AS total_quantity 
                    FROM catch_reports cr 
                    WHERE cr.UID = ? 
                    GROUP BY DATE(cr.catch_date) 
                    ORDER BY DATE(cr.catch_date)";
$stmt = $conn->prepare($catchTrendQuery);
$stmt->bind_param("i", $uid);
$stmt->execute();
$catchTrendData = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("layouts/head.php"); ?>
    <style>
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .stat-card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
        .pagination .page-link {
            color: #007bff;
        }
        .pagination .page-link:hover {
            background-color: #e9ecef;
        }
        .no-data {
            text-align: center;
            font-size: 1.2rem;
            color: #6c757d;
        }
    </style>
</head>

<body style="background-color: #e8f4fc;">
    <?php include("layouts/navbar.php"); ?>

    <main class="container my-4" style="background-color: #e8f4fc;">
        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="card text-center shadow-sm stat-card-hover">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-fish me-2"></i>Total Catch Reports</h5>
                        <p class="card-text fs-4 fw-bold"><?= htmlspecialchars($totalCatchReports) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card text-center shadow-sm stat-card-hover">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-exclamation-triangle me-2"></i>Total Violations</h5>
                        <p class="card-text fs-4 fw-bold"><?= htmlspecialchars($totalViolations) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card text-center shadow-sm stat-card-hover">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-money-bill-wave me-2"></i>Pending Penalty</h5>
                        <p class="card-text fs-4 fw-bold">₱<?= number_format($totalPendingPenalty, 2) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Catch Reports by Species</h5>
                        <?php if ($speciesCatchData->num_rows > 0): ?>
                            <canvas id="speciesCatchChart"></canvas>
                        <?php else: ?>
                            <p class="no-data text-center">No data available for catch reports by species.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Violations Status</h5>
                        <div class="d-flex" style=" width: 400px; height: 300px;">
                            <?php if ($violationsResolvedData->num_rows > 0): ?>
                                <canvas id="violationsChart"></canvas>
                            <?php else: ?>
                                <p class="no-data mt-3">No data available for violations status.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Catch Trend Over Time</h5>
                        <?php if ($catchTrendData->num_rows > 0): ?>
                            <canvas id="catchTrendChart"></canvas>
                        <?php else: ?>
                            <p class="no-data">No data available for catch trend over time.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Catch Reports</h5>
                        <div class="table-responsive">
                            <?php if ($catchReports->num_rows > 0): ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Species</th>
                                            <th>Quantity</th>
                                            <th>Size (cm)</th>
                                            <th>Catch Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $catchReports->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['CRID']) ?></td>
                                                <td><?= htmlspecialchars($row['species_name']) ?></td>
                                                <td><?= htmlspecialchars($row['quantity']) ?></td>
                                                <td><?= htmlspecialchars($row['size_cm'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($row['catch_date']) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="no-data">No catch reports available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Violations</h5>
                        <div class="table-responsive">
                            <?php if ($violations->num_rows > 0): ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Species</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Penalty</th>
                                            <th>Resolved</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $violations->fetch_assoc()): ?>
                                            <tr class="<?= $row['resolved'] ? '' : 'table-danger' ?>">
                                                <td><?= htmlspecialchars($row['id']) ?></td>
                                                <td><?= htmlspecialchars($row['species_name'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($row['date']) ?></td>
                                                <td><?= htmlspecialchars($row['description']) ?></td>
                                                <td><?= htmlspecialchars($row['penalty'] ?? 'N/A') ?></td>
                                                <td><?= $row['resolved'] ? 'Yes' : 'No' ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="no-data">No violations available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include("layouts/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        <?php if ($speciesCatchData->num_rows > 0): ?>
            // Prepare data for Species Catch Chart
            const speciesCatchLabels = [];
            const speciesCatchValues = [];
            <?php while ($row = $speciesCatchData->fetch_assoc()): ?>
                speciesCatchLabels.push("<?= htmlspecialchars($row['species_name']) ?>");
                speciesCatchValues.push(<?= htmlspecialchars($row['total_quantity']) ?>);
            <?php endwhile; ?>

            // Render Species Catch Chart
            const speciesCatchCtx = document.getElementById('speciesCatchChart').getContext('2d');
            new Chart(speciesCatchCtx, {
                type: 'bar',
                data: {
                    labels: speciesCatchLabels,
                    datasets: [{
                        label: 'Total Quantity',
                        data: speciesCatchValues,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        <?php endif; ?>

        <?php if ($violationsResolvedData->num_rows > 0): ?>
            // Prepare data for Violations Chart
            const violationsLabels = ['Resolved', 'Unresolved'];
            const violationsValues = [0, 0];
            <?php while ($row = $violationsResolvedData->fetch_assoc()): ?>
                violationsValues[<?= $row['resolved'] ? 0 : 1 ?>] = <?= htmlspecialchars($row['count']) ?>;
            <?php endwhile; ?>

            // Render Violations Chart
            const violationsCtx = document.getElementById('violationsChart').getContext('2d');
            new Chart(violationsCtx, {
                type: 'pie',
                data: {
                    labels: violationsLabels,
                    datasets: [{
                        data: violationsValues,
                        backgroundColor: ['rgba(75, 192, 192, 0.6)', 'rgba(255, 99, 132, 0.6)'],
                        borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        <?php endif; ?>

        <?php if ($catchTrendData->num_rows > 0): ?>
            // Prepare data for Catch Trend Chart
            const catchTrendLabels = [];
            const catchTrendValues = [];
            <?php while ($row = $catchTrendData->fetch_assoc()): ?>
                catchTrendLabels.push("<?= htmlspecialchars($row['catch_date']) ?>");
                catchTrendValues.push(<?= htmlspecialchars($row['total_quantity']) ?>);
            <?php endwhile; ?>

            // Render Catch Trend Chart
            const catchTrendCtx = document.getElementById('catchTrendChart').getContext('2d');
            new Chart(catchTrendCtx, {
                type: 'line',
                data: {
                    labels: catchTrendLabels,
                    datasets: [{
                        label: 'Total Quantity',
                        data: catchTrendValues,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        x: { title: { display: true, text: 'Date' } },
                        y: { title: { display: true, text: 'Quantity' } }
                    }
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>