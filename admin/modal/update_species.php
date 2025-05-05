<?php
include '../../assets/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sid = $_POST['sid'];
    $name = $_POST['name'];
    $catch_limit = $_POST['catch_limit'];
    $is_available = $_POST['is_available'];
    $fine_rate = $_POST['fine_rate'];
    $endangered_level = $_POST['endangered_level'];

    $query = "UPDATE species 
              SET name = ?, catch_limit = ?, is_available = ?, fine_rate = ?, endangered_level = ? 
              WHERE SID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('siidsi', $name, $catch_limit, $is_available, $fine_rate, $endangered_level, $sid);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Species updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update species.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
