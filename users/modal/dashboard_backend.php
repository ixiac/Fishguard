<?php

if (!isset($_SESSION['UID'])) {
    header("Location: ../../index.php");
    exit();
}

include '../assets/db.php';

// Fetch total catches
$totalCatchesQuery = "SELECT SUM(quantity) AS total_catches FROM catch_reports";
$totalCatchesResult = $conn->query($totalCatchesQuery);
$totalCatches = $totalCatchesResult ? $totalCatchesResult->fetch_assoc()['total_catches'] ?? 0 : 0;

// Fetch active species
$activeSpeciesQuery = "SELECT COUNT(*) AS active_species FROM species WHERE is_available = 1";
$activeSpeciesResult = $conn->query($activeSpeciesQuery);
$activeSpecies = $activeSpeciesResult ? $activeSpeciesResult->fetch_assoc()['active_species'] ?? 0 : 0;

// Fetch total violations
$violationsCountQuery = "SELECT COUNT(*) AS total_violations FROM violations";
$violationsCountResult = $conn->query($violationsCountQuery);
$totalViolations = $violationsCountResult ? $violationsCountResult->fetch_assoc()['total_violations'] ?? 0 : 0;

// Fetch endangered species
$endangeredSpeciesQuery = "SELECT COUNT(*) AS endangered_species FROM species WHERE endangered_level = 'High'";
$endangeredSpeciesResult = $conn->query($endangeredSpeciesQuery);
$endangeredSpecies = $endangeredSpeciesResult ? $endangeredSpeciesResult->fetch_assoc()['endangered_species'] ?? 0 : 0;

// Fetch user-specific total catches
$UID = $_SESSION['UID'];
$userCatchesQuery = "SELECT SUM(quantity) AS user_catches FROM catch_reports WHERE UID = $UID";
$userCatchesResult = $conn->query($userCatchesQuery);
$userCatches = $userCatchesResult ? $userCatchesResult->fetch_assoc()['user_catches'] ?? 0 : 0;

// Fetch user-specific violations
$userViolationsQuery = "SELECT COUNT(*) AS user_violations FROM violations WHERE UID = $UID";
$userViolationsResult = $conn->query($userViolationsQuery);
$userViolations = $userViolationsResult ? $userViolationsResult->fetch_assoc()['user_violations'] ?? 0 : 0;

// Fetch data for charts
$catchesPerSpecies = $conn->query("SELECT species.name, SUM(catch_reports.quantity) AS count 
                                   FROM catch_reports 
                                   JOIN species ON catch_reports.SID = species.SID 
                                   GROUP BY catch_reports.SID");

$endangeredLevels = $conn->query("SELECT endangered_level, COUNT(*) AS count 
                                  FROM species 
                                  GROUP BY endangered_level");

// Fetch recent catch reports
$recentCatches = $conn->query("SELECT species.name AS species_name, quantity, catch_date 
                               FROM catch_reports 
                               JOIN species ON catch_reports.SID = species.SID 
                               ORDER BY catch_date DESC LIMIT 3");

// Fetch recent violations
$recentViolations = $conn->query("SELECT description, penalty, date 
                                  FROM violations 
                                  ORDER BY date DESC LIMIT 3");
?>
