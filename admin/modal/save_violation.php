<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 1) {
    header("Location: ../index.php");
    exit();
}

require_once '../../assets/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_POST['user'];
    $sid = $_POST['species'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    $penalty = $_POST['penalty'];

    $stmt = $conn->prepare("INSERT INTO violations (UID, SID, date, description, penalty) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissd", $uid, $sid, $date, $description, $penalty);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}

header("HTTP/1.1 405 Method Not Allowed");
exit();
