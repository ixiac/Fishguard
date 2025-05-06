<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 1) {
    header("Location: ../index.php");
    exit();
}

// Include database connection
include '../assets/db.php';

// Fetch current user data
$user_id = $_SESSION['UID']; // Assuming user_id is stored in session
$query = $conn->prepare("SELECT username, name, address, contact_no FROM users WHERE UID = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Header -->
    <?php include 'layouts/head.php'; ?>

    <!-- Custom CSS -->
    <link href="../assets/css/admindash.css" rel="stylesheet">
    <style>
        .settings-card {
            border: 0;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #ffffff;
        }

        .settings-card h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #4e73df;
            margin-bottom: 20px;
        }

        .settings-card .form-label {
            font-weight: bold;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'layouts/sidebar.php'; ?>

            <!-- Main Content -->
            <main id="content" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-5 border-bottom">
                    <h1 class="h2">Settings</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <i class="bi bi-calendar"></i> This week
                        </button>
                    </div>
                </div>

                <!-- User Settings Form -->
                <div class="settings-card">
                    <h2>Edit Your Credentials</h2>
                    <form id="settingsForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (Leave blank to keep current password)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact_no" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_no" name="contact_no" value="<?php echo htmlspecialchars($user['contact_no']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success mt-3">Update</button>
                    </form>
                    <div id="responseMessage" class="mt-3"></div>
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ensure the form exists before adding the event listener
            const settingsForm = document.getElementById('settingsForm');
            if (!settingsForm) {
                console.error('Error: settingsForm element not found.');
                return;
            }

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

            // Handle form submission via AJAX
            settingsForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                console.log('Submitting form...'); // Debugging log

                fetch('modal/update_settings.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response received:', response); // Debugging log
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data); // Debugging log
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error); // Debugging log
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                });
            });
        });
    </script>
</body>

</html>