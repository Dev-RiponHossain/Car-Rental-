<?php
session_start();
include('includes/config.php');

$sql = "SELECT COUNT(*) as total FROM tblbooking WHERE Status = 0";
$query = $dbh->prepare($sql);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

echo json_encode(['new_bookings' => $result['total']]);
?>
