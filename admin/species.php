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
                    <h1 class="h2">Species</h1>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addSpeciesModal">
                        <i class="bi bi-plus-circle"></i> Add New Species
                    </button>
                </div>

                <!-- Species Nav Tabs -->
                <div class="card mt-3">
                    <div class="card-header ps-3">
                        <ul class="nav nav-tabs card-header-tabs" id="speciesTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">All Species</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="low-tab" data-bs-toggle="tab" data-bs-target="#low" type="button" role="tab" aria-controls="low" aria-selected="false">Low</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="medium-tab" data-bs-toggle="tab" data-bs-target="#medium" type="button" role="tab" aria-controls="medium" aria-selected="false">Medium</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="high-tab" data-bs-toggle="tab" data-bs-target="#high" type="button" role="tab" aria-controls="high" aria-selected="false">High</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="speciesTabsContent">
                            <!-- All Species Tab -->
                            <div class="tab-pane fade show active mt-4" id="all" role="tabpanel" aria-labelledby="all-tab">
                                <div class="table-responsive">
                                    <table id="allSpeciesTable" class="table table-striped table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Catch Limit</th>
                                                <th>Availability</th>
                                                <th>Fine Rate</th>
                                                <th>Endangered Level</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include '../assets/db.php';
                                            $query = "SELECT name, catch_limit, is_available, fine_rate, endangered_level FROM species";
                                            $result = mysqli_query($conn, $query);

                                            if ($result) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>
                                                            <td>{$row['name']}</td>
                                                            <td>{$row['catch_limit']}</td>
                                                            <td>" . ($row['is_available'] ? 'Yes' : 'No') . "</td>
                                                            <td>₱" . number_format($row['fine_rate'], 2, '.', ',') . "</td>
                                                            <td>{$row['endangered_level']}</td>
                                                            <td>
                                                                <button type='button' class='btn btn-link btn-sm text-primary' title='Edit' data-bs-toggle='modal' data-bs-target='#editSpeciesModal' 
                                                                    data-name='{$row['name']}' 
                                                                    data-catch_limit='{$row['catch_limit']}' 
                                                                    data-is_available='{$row['is_available']}' 
                                                                    data-fine_rate='{$row['fine_rate']}' 
                                                                    data-endangered_level='{$row['endangered_level']}'>
                                                                    <i class='bi bi-pencil'></i>
                                                                </button>
                                                                <button type='button' class='btn btn-link btn-sm text-danger delete-species-btn' title='Delete' data-name='{$row['name']}'>
                                                                    <i class='bi bi-trash'></i>
                                                                </button>
                                                            </td>
                                                          </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6'>No species found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Low Tab -->
                            <div class="tab-pane fade mt-4" id="low" role="tabpanel" aria-labelledby="low-tab">
                                <div class="table-responsive">
                                    <table id="lowSpeciesTable" class="table table-striped table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Catch Limit</th>
                                                <th>Availability</th>
                                                <th>Fine Rate</th>
                                                <th>Endangered Level</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include '../assets/db.php';
                                            $query = "SELECT name, catch_limit, is_available, fine_rate, endangered_level FROM species WHERE endangered_level = 'low'";
                                            $result = mysqli_query($conn, $query);

                                            if ($result) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>
                                                            <td>{$row['name']}</td>
                                                            <td>{$row['catch_limit']}</td>
                                                            <td>" . ($row['is_available'] ? 'Yes' : 'No') . "</td>
                                                            <td>{$row['fine_rate']}</td>
                                                            <td>{$row['endangered_level']}</td>
                                                            <td>
                                                                <button type='button' class='btn btn-link btn-sm text-primary' title='Edit' data-bs-toggle='modal' data-bs-target='#editSpeciesModal' 
                                                                    data-name='{$row['name']}' 
                                                                    data-catch_limit='{$row['catch_limit']}' 
                                                                    data-is_available='{$row['is_available']}' 
                                                                    data-fine_rate='{$row['fine_rate']}' 
                                                                    data-endangered_level='{$row['endangered_level']}'>
                                                                    <i class='bi bi-pencil'></i>
                                                                </button>
                                                                <button type='button' class='btn btn-link btn-sm text-danger delete-species-btn' title='Delete' data-name='{$row['name']}'>
                                                                    <i class='bi bi-trash'></i>
                                                                </button>
                                                            </td>
                                                          </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6'>No species found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Medium Tab -->
                            <div class="tab-pane fade mt-4" id="medium" role="tabpanel" aria-labelledby="medium-tab">
                                <div class="table-responsive">
                                    <table id="mediumSpeciesTable" class="table table-striped table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Catch Limit</th>
                                                <th>Availability</th>
                                                <th>Fine Rate</th>
                                                <th>Endangered Level</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT name, catch_limit, is_available, fine_rate, endangered_level FROM species WHERE endangered_level = 'medium'";
                                            $result = mysqli_query($conn, $query);

                                            if ($result) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>
                                                            <td>{$row['name']}</td>
                                                            <td>{$row['catch_limit']}</td>
                                                            <td>" . ($row['is_available'] ? 'Yes' : 'No') . "</td>
                                                            <td>{$row['fine_rate']}</td>
                                                            <td>{$row['endangered_level']}</td>
                                                            <td>
                                                                <button type='button' class='btn btn-link btn-sm text-primary' title='Edit' data-bs-toggle='modal' data-bs-target='#editSpeciesModal' 
                                                                    data-name='{$row['name']}' 
                                                                    data-catch_limit='{$row['catch_limit']}' 
                                                                    data-is_available='{$row['is_available']}' 
                                                                    data-fine_rate='{$row['fine_rate']}' 
                                                                    data-endangered_level='{$row['endangered_level']}'>
                                                                    <i class='bi bi-pencil'></i>
                                                                </button>
                                                                <button type='button' class='btn btn-link btn-sm text-danger delete-species-btn' title='Delete' data-name='{$row['name']}'>
                                                                    <i class='bi bi-trash'></i>
                                                                </button>
                                                            </td>
                                                          </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6'>No species found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- High Tab -->
                            <div class="tab-pane fade mt-4" id="high" role="tabpanel" aria-labelledby="high-tab">
                                <div class="table-responsive">
                                    <table id="highSpeciesTable" class="table table-striped table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Catch Limit</th>
                                                <th>Availability</th>
                                                <th>Fine Rate</th>
                                                <th>Endangered Level</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT name, catch_limit, is_available, fine_rate, endangered_level FROM species WHERE endangered_level = 'high'";
                                            $result = mysqli_query($conn, $query);

                                            if ($result) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>
                                                            <td>{$row['name']}</td>
                                                            <td>{$row['catch_limit']}</td>
                                                            <td>" . ($row['is_available'] ? 'Yes' : 'No') . "</td>
                                                            <td>{$row['fine_rate']}</td>
                                                            <td>{$row['endangered_level']}</td>
                                                            <td>
                                                                <button type='button' class='btn btn-link btn-sm text-primary' title='Edit' data-bs-toggle='modal' data-bs-target='#editSpeciesModal' 
                                                                    data-name='{$row['name']}' 
                                                                    data-catch_limit='{$row['catch_limit']}' 
                                                                    data-is_available='{$row['is_available']}' 
                                                                    data-fine_rate='{$row['fine_rate']}' 
                                                                    data-endangered_level='{$row['endangered_level']}'>
                                                                    <i class='bi bi-pencil'></i>
                                                                </button>
                                                                <button type='button' class='btn btn-link btn-sm text-danger delete-species-btn' title='Delete' data-name='{$row['name']}'>
                                                                    <i class='bi bi-trash'></i>
                                                                </button>
                                                            </td>
                                                          </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6'>No species found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Tab Content -->

                <!-- Add Species Modal -->
                <div class="modal fade" id="addSpeciesModal" tabindex="-1" aria-labelledby="addSpeciesModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="addSpeciesForm" action="modal/add_species.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addSpeciesModalLabel">Add New Species</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="addName" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="addName" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="addCatchLimit" class="form-label">Catch Limit</label>
                                        <input type="number" class="form-control" id="addCatchLimit" name="catch_limit" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="addAvailability" class="form-label">Availability</label>
                                        <select class="form-select" id="addAvailability" name="is_available" required>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="addFineRate" class="form-label">Fine Rate</label>
                                        <input type="number" step="0.01" class="form-control" id="addFineRate" name="fine_rate" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="addEndangeredLevel" class="form-label">Endangered Level</label>
                                        <select class="form-select" id="addEndangeredLevel" name="endangered_level" required>
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Add Species</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Species Modal -->
                <div class="modal fade" id="editSpeciesModal" tabindex="-1" aria-labelledby="editSpeciesModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="editSpeciesForm" action="modal/update_species.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editSpeciesModalLabel">Edit Species</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="sid" id="editSpeciesId">
                                    <div class="mb-3">
                                        <label for="editName" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="editName" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editCatchLimit" class="form-label">Catch Limit</label>
                                        <input type="number" class="form-control" id="editCatchLimit" name="catch_limit" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editAvailability" class="form-label">Availability</label>
                                        <select class="form-select" id="editAvailability" name="is_available" required>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editFineRate" class="form-label">Fine Rate</label>
                                        <input type="number" step="0.01" class="form-control" id="editFineRate" name="fine_rate" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEndangeredLevel" class="form-label">Endangered Level</label>
                                        <select class="form-select" id="editEndangeredLevel" name="endangered_level" required>
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Save Changes</button>
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

    <!-- SweetAlert Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize DataTables
            $('#allSpeciesTable').DataTable();
            $('#lowSpeciesTable').DataTable();
            $('#mediumSpeciesTable').DataTable();
            $('#highSpeciesTable').DataTable();

            // Handle delete species button click
            document.querySelectorAll('.delete-species-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const speciesId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`modal/delete_species.php?id=${speciesId}`, {
                                method: 'GET'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'The species has been deleted.',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        location.reload(); // Reload the page to reflect changes
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message || 'Failed to delete the species.',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Unexpected Error',
                                    text: error.message || 'An unexpected error occurred.',
                                    confirmButtonText: 'OK'
                                });
                            });
                        }
                    });
                });
            });

            // Populate modal with species data on edit button click
            const editSpeciesModal = document.getElementById('editSpeciesModal');
            if (editSpeciesModal) {
                editSpeciesModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;

                    // Fetch data from button attributes
                    const sid = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const catchLimit = button.getAttribute('data-catch_limit');
                    const isAvailable = button.getAttribute('data-is_available');
                    const fineRate = button.getAttribute('data-fine_rate');
                    const endangeredLevel = button.getAttribute('data-endangered_level');

                    // Populate modal fields
                    editSpeciesModal.querySelector('#editSpeciesId').value = sid || '';
                    editSpeciesModal.querySelector('#editName').value = name || '';
                    editSpeciesModal.querySelector('#editCatchLimit').value = catchLimit || '';
                    editSpeciesModal.querySelector('#editAvailability').value = isAvailable || '';
                    editSpeciesModal.querySelector('#editFineRate').value = fineRate || '';
                    editSpeciesModal.querySelector('#editEndangeredLevel').value = endangeredLevel || '';
                });
            }

            // Handle edit species form submission
            const editSpeciesForm = document.getElementById('editSpeciesForm');
            editSpeciesForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(editSpeciesForm);

                fetch('modal/update_species.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || 'Species updated successfully!',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload(); // Reload the page to reflect changes
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to update species.',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Unexpected Error',
                        text: error.message || 'An unexpected error occurred.',
                        confirmButtonText: 'OK'
                    });
                });
            });

            // Handle add species form submission
            const addSpeciesForm = document.getElementById('addSpeciesForm');
            addSpeciesForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(addSpeciesForm);

                fetch('modal/add_species.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || 'Species added successfully!',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload(); // Reload the page to reflect changes
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to add species.',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Unexpected Error',
                        text: error.message || 'An unexpected error occurred.',
                        confirmButtonText: 'OK'
                    });
                });
            });
        });
    </script>
</body>

</html>