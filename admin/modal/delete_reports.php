<?php
require_once '../../assets/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $CRID = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM catch_reports WHERE CRID = ?");
    $stmt->bind_param("i", $CRID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit();
}
?>
