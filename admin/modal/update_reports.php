<?php
require_once '../../assets/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $CRID = $_GET['id'];

    $stmt = $conn->prepare("SELECT CRID, UID, SID, quantity, size_cm, catch_date FROM catch_reports WHERE CRID = ?");
    $stmt->bind_param("i", $CRID);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $report = $result->fetch_assoc();
            echo json_encode(['success' => true, 'report' => $report]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Report not found']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $CRID = $_POST['id'];
    $quantity = $_POST['quantity'];
    $size_cm = $_POST['size_cm'];
    $catch_date = $_POST['catch_date'];

    $stmt = $conn->prepare("UPDATE catch_reports SET quantity = ?, size_cm = ?, catch_date = ? WHERE CRID = ?");
    $stmt->bind_param("ddsi", $quantity, $size_cm, $catch_date, $CRID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit();
}
?>
