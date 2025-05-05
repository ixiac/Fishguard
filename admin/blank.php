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
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Blank</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <i class="bi bi-calendar"></i> This week
                        </button>
                    </div>
                </div>

                <!-- New Container -->
                <div style="min-height: 505px;">F
                    <!-- Add your content here -->
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