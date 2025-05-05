<?php
include '../assets/db.php';

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    // Prepare and execute the delete query
    $query = "DELETE FROM users WHERE UID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete the user.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
