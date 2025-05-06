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
                    <h1 class="h2">Users</h1>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus"></i> Add New User
                    </button>
                </div>

                <!-- Users Nav Tabs -->
                <div class="card mt-3">
                    <div class="card-header ps-3">
                        <ul class="nav nav-tabs card-header-tabs" id="userTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all"
                                    type="button" role="tab" aria-controls="all" aria-selected="true">All
                                    Accounts</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="admins-tab" data-bs-toggle="tab" data-bs-target="#admins"
                                    type="button" role="tab" aria-controls="admins"
                                    aria-selected="false">Admins</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="customers-tab" data-bs-toggle="tab"
                                    data-bs-target="#customers" type="button" role="tab" aria-controls="customers"
                                    aria-selected="false">Fishermen</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="userTabsContent">
                            <!-- All Accounts Tab -->
                            <div class="tab-pane fade show active mt-4" id="all" role="tabpanel"
                                aria-labelledby="all-tab">
                                <div class="table-responsive">
                                    <table id="allUsersTable" class="table table-striped table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Name</th>
                                                <th>Address</th>
                                                <th>Contact No</th>
                                                <th>Role</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include '../assets/db.php';
                                            $query = "SELECT UID, username, name, address, contact_no, role FROM users";
                                            $result = mysqli_query($conn, $query);

                                            if ($result) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>
                                                            <td>{$row['username']}</td>
                                                            <td>{$row['name']}</td>
                                                            <td>{$row['address']}</td>
                                                            <td>{$row['contact_no']}</td>
                                                            <td>" . ($row['role'] == 1 ? 'Admin' : 'Fisherman') . "</td>
                                                            <td>
                                                                <button type='button' class='btn btn-link btn-sm text-primary' title='Edit' data-bs-toggle='modal' data-bs-target='#editUserModal' 
                                                                    data-uid='{$row['UID']}' 
                                                                    data-username='{$row['username']}' 
                                                                    data-name='{$row['name']}' 
                                                                    data-address='{$row['address']}' 
                                                                    data-contact='{$row['contact_no']}'>
                                                                    <i class='bi bi-pencil'></i>
                                                                </button>
                                                                <button type='button' class='btn btn-link btn-sm text-danger delete-user-btn' title='Delete' data-uid='{$row['UID']}'>
                                                                    <i class='bi bi-trash'></i>
                                                                </button>
                                                            </td>
                                                          </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6'>No users found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Admins Tab -->
                            <div class="tab-pane fade" id="admins" role="tabpanel" aria-labelledby="admins-tab">
                                <div class="table-responsive">
                                    <table id="adminsTable" class="table table-striped table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Name</th>
                                                <th>Address</th>
                                                <th>Contact No</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT username, name, address, contact_no FROM users WHERE role = 1";
                                            $result = mysqli_query($conn, $query);

                                            if ($result) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>
                                                            <td>{$row['username']}</td>
                                                            <td>{$row['name']}</td>
                                                            <td>{$row['address']}</td>
                                                            <td>{$row['contact_no']}</td>
                                                            <td>
                                                                <button type='button' class='btn btn-link btn-sm text-primary' title='Edit' data-bs-toggle='modal' data-bs-target='#editUserModal' 
                                                                    data-username='{$row['username']}' 
                                                                    data-name='{$row['name']}' 
                                                                    data-address='{$row['address']}' 
                                                                    data-contact='{$row['contact_no']}'>
                                                                    <i class='bi bi-pencil'></i>
                                                                </button>
                                                                <a href='delete_user.php?username={$row['username']}' class='btn btn-link btn-sm text-danger' title='Delete' onclick='return confirm(\"Are you sure?\")'>
                                                                    <i class='bi bi-trash'></i>
                                                                </a>
                                                            </td>
                                                          </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='5'>No admins found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Customers Tab -->
                            <div class="tab-pane fade" id="customers" role="tabpanel" aria-labelledby="customers-tab">
                                <div class="table-responsive">
                                    <table id="customersTable" class="table table-striped table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Name</th>
                                                <th>Address</th>
                                                <th>Contact No</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT username, name, address, contact_no FROM users WHERE role != 1";
                                            $result = mysqli_query($conn, $query);

                                            if ($result) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>
                                                            <td>{$row['username']}</td>
                                                            <td>{$row['name']}</td>
                                                            <td>{$row['address']}</td>
                                                            <td>{$row['contact_no']}</td>
                                                            <td>
                                                                <button type='button' class='btn btn-link btn-sm text-primary' title='Edit' data-bs-toggle='modal' data-bs-target='#editUserModal' 
                                                                    data-username='{$row['username']}' 
                                                                    data-name='{$row['name']}' 
                                                                    data-address='{$row['address']}' 
                                                                    data-contact='{$row['contact_no']}'>
                                                                    <i class='bi bi-pencil'></i>
                                                                </button>
                                                                <a href='delete_user.php?username={$row['username']}' class='btn btn-link btn-sm text-danger' title='Delete' onclick='return confirm(\"Are you sure?\")'>
                                                                    <i class='bi bi-trash'></i>
                                                                </a>
                                                            </td>
                                                          </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='5'>No customers found</td></tr>";
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

                <!-- Edit User Modal -->
                <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="editUserForm" action="modal/update_user.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="uid" id="editUserId">
                                    <div class="mb-3">
                                        <label for="editUsername" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="editUsername" name="username"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editName" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="editName" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editAddress" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="editAddress" name="address"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editContact" class="form-label">Contact No</label>
                                        <input type="text" class="form-control" id="editContact" name="contact_no"
                                            required>
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

                <!-- Add User Modal -->
                <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="addUserForm" action="modal/add_user.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="addUsername" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="addUsername" name="username"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="addPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="addPassword" name="password"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="addName" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="addName" name="name" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="addAddress" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="addAddress" name="address"
                                                required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="addContact" class="form-label">Contact No</label>
                                            <input type="text" class="form-control" id="addContact" name="contact_no"
                                                required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="addRole" class="form-label">Role</label>
                                        <select class="form-select" id="addRole" name="role" required>
                                            <option value="1">Admin</option>
                                            <option value="2">Fisherman</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Add User</button>
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
            // Initialize DataTables with pagination, search, and sorting
            if (document.querySelector('#allUsersTable')) {
                $('#allUsersTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true
                });
            }
            if (document.querySelector('#adminsTable')) {
                $('#adminsTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true
                });
            }
            if (document.querySelector('#customersTable')) {
                $('#customersTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true
                });
            }

            // Toggle sidebar on button click
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            if (sidebarCollapse) { // Check if the element exists
                sidebarCollapse.addEventListener('click', function () {
                    document.getElementById('sidebar').classList.toggle('active');
                    document.querySelector('.overlay').classList.toggle('active');
                });
            }

            // Close sidebar when clicking on overlay
            const overlay = document.querySelector('.overlay');
            if (overlay) { // Check if the overlay exists
                overlay.addEventListener('click', function () {
                    document.getElementById('sidebar').classList.remove('active');
                    this.classList.remove('active');
                });
            }

            // Populate modal with user data on edit button click
            const editUserModal = document.getElementById('editUserModal');
            if (editUserModal) { // Check if the modal exists
                editUserModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;

                    // Debugging: Log the data attributes to verify they are being passed correctly
                    console.log('Data Attributes:', {
                        uid: button.getAttribute('data-uid'),
                        username: button.getAttribute('data-username'),
                        name: button.getAttribute('data-name'),
                        address: button.getAttribute('data-address'),
                        contact: button.getAttribute('data-contact')
                    });

                    // Fetch data from button attributes
                    const uid = button.getAttribute('data-uid');
                    const username = button.getAttribute('data-username');
                    const name = button.getAttribute('data-name');
                    const address = button.getAttribute('data-address');
                    const contact = button.getAttribute('data-contact');

                    // Populate modal fields
                    editUserModal.querySelector('#editUserId').value = uid || '';
                    editUserModal.querySelector('#editUsername').value = username || '';
                    editUserModal.querySelector('#editName').value = name || '';
                    editUserModal.querySelector('#editAddress').value = address || '';
                    editUserModal.querySelector('#editContact').value = contact || '';

                    // Debugging: Log the modal fields to verify they are populated
                    console.log('Modal Fields:', {
                        uid: editUserModal.querySelector('#editUserId').value,
                        username: editUserModal.querySelector('#editUsername').value,
                        name: editUserModal.querySelector('#editName').value,
                        address: editUserModal.querySelector('#editAddress').value,
                        contact: editUserModal.querySelector('#editContact').value
                    });
                });
            }

            // Handle form submission
            const editUserForm = document.getElementById('editUserForm');
            editUserForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(editUserForm);

                fetch('modal/update_user.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Unexpected Error',
                            text: 'An unexpected error occurred. Please check the console for details.',
                            confirmButtonText: 'OK'
                        });
                    })
                    .then(data => {
                        if (data && data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'User updated successfully!',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else if (data) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Error updating user.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
            });

            // Handle add user form submission
            const addUserForm = document.getElementById('addUserForm');
            addUserForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(addUserForm);

                fetch('modal/add_user.php', {
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
                                text: 'User added successfully!',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Error adding user.',
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

            // Handle delete user button click
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const userId = this.getAttribute('data-uid');

                    // Show SweetAlert confirmation dialog
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
                            // Send delete request to the backend
                            fetch(`modal/delete_user.php?id=${userId}`, {
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
                                            text: 'The user has been deleted.',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            location.reload(); // Reload the page to reflect changes
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message || 'Failed to delete the user.',
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
        });
    </script>
</body>

</html>