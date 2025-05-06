<?php
$active = basename($_SERVER['PHP_SELF']);
?>
<div id="sidebar" class="col-md-3 col-lg-2 d-md-block" style="background-color: #0e79b1; min-height: 100vh;">
    <div class="position-sticky">
        <div class="d-flex align-items-center p-3 mb-3 border-bottom">
            <img src="../assets/img/avatar.png" class="rounded-circle me-3" alt="admin avatar" width="50" height="50">
            <div>
                <h6 class="mb-0">
                    <?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin User'); ?>
                </h6>
                <small class="text-warning">Administrator</small>
            </div>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $active == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="bi bi-house-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active == 'users.php' ? 'active' : ''; ?>" href="users.php">
                    <i class="bi bi-people-fill"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active == 'species.php' ? 'active' : ''; ?>" href="species.php">
                    <i class="fas fa-fish"></i> Species
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                    <i class="bi bi-file-earmark-fill"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active == 'violations.php' ? 'active' : ''; ?>" href="violations.php">
                    <i class="bi bi-shield-fill-exclamation"></i> Violations
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active == 'analytics.php' ? 'active' : ''; ?>" href="analytics.php">
                    <i class="bi bi-graph-up-arrow"></i> Analytics
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="bi bi-gear-fill"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $active == 'about.php' ? 'active' : ''; ?>" href="about.php">
                    <i class="bi bi-info-circle-fill"></i> About
                </a>
            </li>
        </ul>
    </div>
</div>