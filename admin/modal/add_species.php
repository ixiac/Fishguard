<?php
include '../../assets/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $catch_limit = $_POST['catch_limit'];
    $is_available = $_POST['is_available'];
    $fine_rate = $_POST['fine_rate'];
    $endangered_level = $_POST['endangered_level'];

    $query = "INSERT INTO species (name, catch_limit, is_available, fine_rate, endangered_level) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('siids', $name, $catch_limit, $is_available, $fine_rate, $endangered_level);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Species added successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add species.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
