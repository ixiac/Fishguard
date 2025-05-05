<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 1) {
    header("Location: ../index.php");
    exit();
}

require_once '../assets/db.php'; // Adjust the path to your database connection file

// Handle AJAX request for DataTable data
if (isset($_GET['fetch']) && $_GET['fetch'] === 'reports') {
    header('Content-Type: application/json');

    $sql = "SELECT 
                cr.CRID, 
                u.name AS Fisherman, 
                COALESCE(s.name, 'Unidentified') AS Species, 
                cr.quantity, 
                cr.size_cm, 
                DATE_FORMAT(cr.catch_date, '%M %d, %Y') AS catch_date,
                s.catch_limit
            FROM catch_reports cr
            JOIN users u ON cr.UID = u.UID
            LEFT JOIN species s ON cr.SID = s.SID";
    $result = $conn->query($sql);

    $data = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['exceeds_limit'] = $row['quantity'] > $row['catch_limit']; // Add flag for exceeding limit
            $data[] = $row;
        }
    } else {
        echo json_encode(['error' => $conn->error]);
        exit();
    }

    echo json_encode(['data' => $data]);
    exit();
}

// Handle Add Report Backend Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $fisherman = $_POST['fisherman'];
    $species = $_POST['species'];    
    $quantity = $_POST['quantity'];
    $size_cm = $_POST['size_cm'];
    $catch_date = $_POST['catch_date'];

    $stmt = $conn->prepare("INSERT INTO catch_reports (UID, SID, quantity, size_cm, catch_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iidds", $fisherman, $species, $quantity, $size_cm, $catch_date);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => $stmt->error]);
    }
    exit();
}

// Handle Update Report Backend Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $CRID = $_POST['id'];
    $quantity = $_POST['quantity'];
    $size_cm = $_POST['size_cm'];
    $catch_date = $_POST['catch_date'];

    $stmt = $conn->prepare("UPDATE catch_reports SET quantity = ?, size_cm = ?, catch_date = ? WHERE CRID = ?");
    $stmt->bind_param("ddsi", $quantity, $size_cm, $catch_date, $CRID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => $stmt->error]);
    }
    exit();
}

// Handle Delete Report Backend Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $CRID = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM catch_reports WHERE CRID = ?");
    $stmt->bind_param("i", $CRID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => $stmt->error]);
    }
    exit();
}

// Fetch Fisherman and Species for dropdowns
$fishermen = $conn->query("SELECT UID, name FROM users");
$species = $conn->query("SELECT SID, name FROM species");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Header -->
    <?php include 'layouts/head.php'; ?>

    <!-- Custom CSS -->
    <link href="../assets/css/admindash.css" rel="stylesheet">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Include SweetAlert2 CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>

    <div class="container-fluid"></div>
    </div>
    <div class="row">
        <?php include 'layouts/sidebar.php'; ?>

        <!-- Main Content -->
        <main id="content" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Reports</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addReportModal">
                        Add New Report
                    </button>
                </div>
            </div>

            <!-- Add Report Modal -->
            <div class="modal fade" id="addReportModal" tabindex="-1" aria-labelledby="addReportModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="addReportForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addReportModalLabel">Add New Report</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="fisherman" class="form-label">Fisherman</label>
                                    <select class="form-control" id="fisherman" name="fisherman" required>
                                        <option value="" disabled selected>Select Fisherman</option>
                                        <?php while ($row = $fishermen->fetch_assoc()): ?>
                                            <option value="<?= $row['UID'] ?>"><?= htmlspecialchars($row['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="species" class="form-label">Species</label>
                                    <select class="form-control" id="species" name="species" required>
                                        <option value="" disabled selected>Select Species</option>
                                        <?php while ($row = $species->fetch_assoc()): ?>
                                            <option value="<?= $row['SID'] ?>"><?= htmlspecialchars($row['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                                </div>
                                <div class="mb-3">
                                    <label for="size_cm" class="form-label">Size (cm)</label>
                                    <input type="number" class="form-control" id="size_cm" name="size_cm" required>
                                </div>
                                <div class="mb-3">
                                    <label for="catch_date" class="form-label">Catch Date</label>
                                    <input type="date" class="form-control" id="catch_date" name="catch_date" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Report Modal -->
            <div class="modal fade" id="editReportModal" tabindex="-1" aria-labelledby="editReportModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="editReportForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editReportModalLabel">Edit Report</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="editReportId" name="id">
                                <div class="mb-3">
                                    <label for="editQuantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="editQuantity" name="quantity"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="editSizeCm" class="form-label">Size (cm)</label>
                                    <input type="number" class="form-control" id="editSizeCm" name="size_cm" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editCatchDate" class="form-label">Catch Date</label>
                                    <input type="date" class="form-control" id="editCatchDate" name="catch_date"
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- New Container -->
            <div style="min-height: 505px;">
                <!-- DataTable -->
                <table id="reportsTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <!-- Removed <th>ID</th> -->
                            <th>Fisherman</th>
                            <th>Caught</th>
                            <th>Quantity</th>
                            <th>Size (cm)</th>
                            <th>Catch Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
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
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Custom JS -->
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

            // Initialize DataTable
            const table = $('#reportsTable').DataTable({
                ajax: 'reports.php?fetch=reports',
                columns: [
                    { data: 'Fisherman' },
                    { data: 'Species' },
                    {
                        data: 'quantity',
                        render: function (data, type, row) {
                            // Check if quantity exceeds the catch limit
                            if (row.exceeds_limit) {
                                return `<span style="color: red; font-weight: bold;">${data}</span>`;
                            }
                            return data;
                        }
                    },
                    { data: 'size_cm' },
                    { data: 'catch_date' },
                    {
                        data: null,
                        className: "center",
                        defaultContent: `
                            <a href="javascript:void(0)" class="edit-btn text-primary text-decoration-none" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="javascript:void(0)" class="delete-btn text-danger text-decoration-none ms-2" title="Delete">
                                <i class="bi bi-trash"></i>
                            </a>
                        `
                    }
                ]
            });

            // Handle Add Report Form Submission
            $('#addReportForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'reports.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function () {
                        $('#addReportModal').modal('hide');
                        $('#addReportForm')[0].reset();
                        $('#reportsTable').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Report Added',
                            text: 'The report has been successfully added!',
                            confirmButtonText: 'OK'
                        });
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add the report. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Handle Edit Button Click
            $('#reportsTable').on('click', '.edit-btn', function () {
                const rowData = table.row($(this).parents('tr')).data();
                $.ajax({
                    url: 'modal/update_reports.php',
                    type: 'GET',
                    data: { id: rowData.CRID },
                    success: function (response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            $('#editReportId').val(data.report.CRID);
                            $('#editQuantity').val(data.report.quantity);
                            $('#editSizeCm').val(data.report.size_cm);
                            $('#editCatchDate').val(data.report.catch_date);
                            $('#editReportModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to fetch report data. Please try again.',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to fetch report data. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Handle Edit Report Form Submission
            $('#editReportForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'modal/update_reports.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            $('#editReportModal').modal('hide');
                            $('#reportsTable').DataTable().ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Report Updated',
                                text: 'The report has been successfully updated!',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.error || 'Failed to update the report. Please try again.',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update the report. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Handle Delete Button Click
            $('#reportsTable').on('click', '.delete-btn', function () {
                const rowData = table.row($(this).parents('tr')).data();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'modal/delete_reports.php',
                            type: 'POST',
                            data: { id: rowData.CRID },
                            success: function (response) {
                                const data = JSON.parse(response);
                                if (data.success) {
                                    $('#reportsTable').DataTable().ajax.reload(null, false);
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'The report has been deleted.',
                                        confirmButtonText: 'OK'
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.error || 'Failed to delete the report. Please try again.',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to delete the report. Please try again.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>