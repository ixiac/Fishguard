<?php
include '../assets/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_POST['uid'];
    $username = $_POST['username'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contact_no = $_POST['contact_no'];

    $query = "UPDATE users SET username = ?, name = ?, address = ?, contact_no = ? WHERE UID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssi', $username, $name, $address, $contact_no, $uid);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
