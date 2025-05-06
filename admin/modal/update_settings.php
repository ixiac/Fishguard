<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

include '../../assets/db.php';

$user_id = $_SESSION['UID'];
$username = $_POST['username'] ?? '';
$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
$name = $_POST['name'] ?? '';
$address = $_POST['address'] ?? '';
$contact_no = $_POST['contact_no'] ?? '';

if (empty($username) || empty($name) || empty($address) || empty($contact_no)) {
    echo json_encode(['success' => false, 'message' => 'All fields except password are required.']);
    exit();
}

try {
    if ($password) {
        $update_query = $conn->prepare("UPDATE users SET username = ?, password = ?, name = ?, address = ?, contact_no = ? WHERE UID = ?");
        $update_query->bind_param("sssssi", $username, $password, $name, $address, $contact_no, $user_id);
    } else {
        $update_query = $conn->prepare("UPDATE users SET username = ?, name = ?, address = ?, contact_no = ? WHERE UID = ?");
        $update_query->bind_param("sssii", $username, $name, $address, $contact_no, $user_id);
    }

    if ($update_query->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating profile.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
