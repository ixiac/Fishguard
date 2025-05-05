<?php
include '../../assets/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact_no = mysqli_real_escape_string($conn, $_POST['contact_no']);
    $role = intval($_POST['role']);
    $password = password_hash(mysqli_real_escape_string($conn, $_POST['password']), PASSWORD_BCRYPT);

    $query = "INSERT INTO users (username, name, address, contact_no, role, password) VALUES ('$username', '$name', '$address', '$contact_no', $role, '$password')";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add user.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
