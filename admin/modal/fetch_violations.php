<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 1) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

require_once '../../assets/db.php';

header('Content-Type: application/json');

$query = "SELECT 
            v.id, 
            u.name AS user_name, 
            COALESCE(s.name, 'N/A') AS species_name, 
            DATE_FORMAT(v.date, '%M %d, %Y') AS formatted_date, 
            v.description, 
            v.penalty, 
            v.resolved 
          FROM violations v
          JOIN users u ON v.UID = u.UID
          LEFT JOIN species s ON v.SID = s.SID";

$result = $conn->query($query);

if ($result) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['data' => $data]);
} else {
    echo json_encode(['error' => $conn->error]);
}

$conn->close();
