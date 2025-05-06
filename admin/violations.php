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
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
                    <h1 class="h2">Reports and Violations</h1>
                </div>

                <!-- Reports and Violations Nav Tabs -->
                <div class="card mt-3">
                    <div class="card-header ps-3">
                        <ul class="nav nav-tabs card-header-tabs" id="reportsViolationsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="reports-tab" data-bs-toggle="tab"
                                    data-bs-target="#reports" type="button" role="tab" aria-controls="reports"
                                    aria-selected="true">Reports</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="violations-tab" data-bs-toggle="tab"
                                    data-bs-target="#violations" type="button" role="tab" aria-controls="violations"
                                    aria-selected="false">Violations</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="reportsViolationsTabsContent">
                            <!-- Reports Tab -->
                            <div class="tab-pane fade show active mt-4" id="reports" role="tabpanel"
                                aria-labelledby="reports-tab">
                                <div class="table-responsive">
                                    <table id="reportsTable" class="table table-striped table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <!-- Removed CRID column -->
                                                <th>Fisherman</th>
                                                <th>Caught</th>
                                                <th>Quantity</th>
                                                <th>Size (cm)</th>
                                                <th>Catch Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include '../assets/db.php';
                                            $query = "SELECT cr.CRID, cr.UID, cr.SID, u.name AS user_name, s.name AS species_name, cr.quantity, cr.size_cm, cr.catch_date, s.catch_limit, s.fine_rate 
                                                      FROM catch_reports cr
                                                      JOIN users u ON cr.UID = u.UID
                                                      JOIN species s ON cr.SID = s.SID
                                                      WHERE cr.quantity > s.catch_limit";
                                            $result = mysqli_query($conn, $query);

                                            if ($result) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    $exceededBy = $row['quantity'] - $row['catch_limit'];
                                                    $penalty = $exceededBy * $row['fine_rate'];
                                                    $formattedDate = date('F d, Y', strtotime($row['catch_date'])); // Format the date in PHP
                                                    echo "<tr>
                                                            <!-- Removed CRID column -->
                                                            <td>{$row['user_name']}</td>
                                                            <td>{$row['species_name']}</td>
                                                            <td><span style='color: red; font-weight: bold;'>{$row['quantity']}</span> <small class='text-muted'>(exceeded by {$exceededBy})</small></td>
                                                            <td>{$row['size_cm']}</td>
                                                            <td>{$formattedDate}</td> <!-- Use the formatted date -->
                                                            <td>
                                                                <a href='#' class='text-danger create-violation-btn' 
                                                                    data-crid='{$row['CRID']}' 
                                                                    data-user='{$row['UID']}' 
                                                                    data-species='{$row['SID']}' 
                                                                    data-exceeded='{$exceededBy}' 
                                                                    data-penalty='{$penalty}' 
                                                                    data-date='{$row['catch_date']}'> <!-- Use the raw catch_date -->
                                                                    <i class='bi bi-exclamation-circle-fill' style='font-size: 1.5rem;'></i> <!-- Larger icon -->
                                                                </a>
                                                            </td>
                                                          </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6'>No reports found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Violations Tab -->
                            <div class="tab-pane fade mt-4" id="violations" role="tabpanel"
                                aria-labelledby="violations-tab">
                                <div class="table-responsive">
                                    <table id="violationsTable"
                                        class="table table-striped table-bordered align-middle w-100">
                                        <thead>
                                            <tr>
                                                <!-- Removed ID column -->
                                                <th>Fisherman</th>
                                                <th>Caught</th>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Penalty</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT v.id, u.name AS user_name, s.name AS species_name, DATE_FORMAT(v.date, '%M %d, %Y') AS formatted_date, v.description, v.penalty, v.resolved 
                                                      FROM violations v
                                                      JOIN users u ON v.UID = u.UID
                                                      LEFT JOIN species s ON v.SID = s.SID";
                                            $result = mysqli_query($conn, $query);

                                            if ($result) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    $resolved = (int) $row['resolved']; // Explicitly cast resolved to integer
                                                    $statusBadge = ($resolved === 1)
                                                        ? "<span class='badge bg-success'>Resolved</span>"
                                                        : "<span class='badge bg-danger'>Unresolved</span>";
                                                    echo "<tr>
                                                            <!-- Removed ID column -->
                                                            <td>{$row['user_name']}</td>
                                                            <td>" . ($row['species_name'] ?? 'N/A') . "</td>
                                                            <td>{$row['formatted_date']}</td>
                                                            <td>{$row['description']}</td>
                                                            <td>{$row['penalty']}</td>
                                                            <td>{$statusBadge}</td>
                                                            <td>";
                                                    if ($resolved === 0) {
                                                        echo "<a href='#' class='text-success mark-resolved-btn' data-id='{$row['id']}'>
                                                                <i class='bi bi-check-circle-fill' style='font-size: 1.5rem;'></i> <!-- Larger icon -->
                                                              </a>";
                                                    }
                                                    echo "</td>
                                                          </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='7'>No violations found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Violation Modal -->
                <div class="modal fade" id="violationModal" tabindex="-1" aria-labelledby="violationModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="violationForm">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="violationModalLabel">Create Violation</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="violationUser" class="form-label">Fisherman</label>
                                        <input type="text" class="form-control" id="violationUser" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="violationSpecies" class="form-label">Caught</label>
                                        <input type="text" class="form-control" id="violationSpecies" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="violationDate" class="form-label">Date</label>
                                        <input type="text" class="form-control" id="violationDate" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="violationDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="violationDescription" rows="3"
                                            required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="violationPenalty" class="form-label">Penalty</label>
                                        <input type="text" class="form-control" id="violationPenalty" readonly>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Save Violation</button>
                                </div>
                            </form>
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

    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize DataTables
            $('#reportsTable').DataTable();
            
            // Update the columns configuration for violationsTable
            $('#violationsTable').DataTable({
                ajax: {
                    url: 'modal/fetch_violations.php',
                    type: 'GET',
                    dataSrc: function (json) {
                        if (!json || json.error) {
                            alert('Failed to load violations data. Please check the server response.');
                            return [];
                        }
                        return json.data;
                    },
                    error: function () {
                        alert('An error occurred while fetching violations data.');
                    }
                },
                columns: [
                    // Removed the id column
                    { data: 'user_name' },
                    { data: 'species_name', defaultContent: 'N/A' },
                    { data: 'formatted_date' },
                    { data: 'description' },
                    { data: 'penalty' },
                    {
                        data: 'resolved',
                        render: function (data) {
                            return data == 1
                                ? "<span class='badge bg-success'>Resolved</span>"
                                : "<span class='badge bg-danger'>Unresolved</span>";
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            if (row.resolved == 0) {
                                return `<a href="#" class="text-success mark-resolved-btn" data-id='${row.id}'>
                                            <i class="bi bi-check-circle-fill" style="font-size: 1.5rem;"></i>
                                        </a>`;
                            }
                            return '';
                        }
                    }
                ]
            });

            // Handle Create Violation Button Click
            $(document).on('click', '.create-violation-btn', function () {
                const uid = $(this).data('user');
                const sid = $(this).data('species');
                const exceeded = $(this).data('exceeded');
                const penalty = $(this).data('penalty');
                const date = $(this).data('date');

                $('#violationUser').val(uid);
                $('#violationSpecies').val(sid);
                $('#violationDate').val(date);
                $('#violationPenalty').val(penalty);
                $('#violationDescription').val(`Exceeded by ${exceeded} units.`);

                $('#violationModal').modal('show');
            });

            // Handle Violation Form Submission
            $('#violationForm').on('submit', function (e) {
                e.preventDefault();
                const data = {
                    user: $('#violationUser').val(),
                    species: $('#violationSpecies').val(),
                    date: $('#violationDate').val(),
                    description: $('#violationDescription').val(),
                    penalty: $('#violationPenalty').val()
                };

                $.ajax({
                    url: 'modal/save_violation.php',
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        $('#violationModal').modal('hide');
                        $('#violationsTable').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Violation saved successfully!',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    },
                    error: function () {
                        alert('Failed to save violation.');
                    }
                });
            });

            // Handle Mark as Resolved Button Click
            $(document).on('click', '.mark-resolved-btn', function () {
                const violationId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will mark the violation as resolved.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, mark it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'modal/mark_resolved.php',
                            type: 'POST',
                            data: { id: violationId },
                            success: function (response) {
                                try {
                                    const jsonResponse = JSON.parse(response);
                                    if (jsonResponse.success) {
                                        $('#violationsTable').DataTable().ajax.reload();
                                        Swal.fire(
                                            'Marked!',
                                            'The violation has been marked as resolved.',
                                            'success'
                                        );
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            jsonResponse.message || 'Failed to mark the violation as resolved.',
                                            'error'
                                        );
                                    }
                                } catch (e) {
                                    console.error('Invalid JSON response:', response);
                                    Swal.fire(
                                        'Error!',
                                        'An unexpected error occurred. Please try again.',
                                        'error'
                                    );
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error('AJAX Error:', status, error);
                                Swal.fire(
                                    'Error!',
                                    'An error occurred while processing your request.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>