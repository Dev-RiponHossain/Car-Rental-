<?php

include('includes/config.php');

header('Content-Type: application/json');

$sql = "SELECT COUNT(*) AS due_count FROM tbl_due_payments WHERE status = 'pending'";
$query = $dbh->prepare($sql);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

echo json_encode(['due_payments' => (int)$result['due_count']]);
?>
