<?php
session_start();

if (!isset($_SESSION['UID'])) {
    header("Location: ../login.php");
    exit();
}

include '../assets/db.php';

// Fetch current user data
$user_id = $_SESSION['UID'];
$query = $conn->prepare("SELECT username, name, address, contact_no FROM users WHERE UID = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("layouts/head.php"); ?>
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

<body style="background-color: #e8f4fc;">
    <?php include("layouts/navbar.php"); ?>

    <main class="container my-4">
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
    </main>

    <?php include("layouts/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const settingsForm = document.getElementById('settingsForm');
            settingsForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('modal/update_settings.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
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