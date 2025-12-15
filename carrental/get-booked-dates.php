<?php
include('includes/config.php');

if (isset($_POST['vhid'])) {
    $vhid = $_POST['vhid'];
    $stmt = $dbh->prepare("SELECT FromDate, ToDate FROM tblbooking WHERE VehicleId = :vhid AND Status != 2");
    $stmt->bindParam(':vhid', $vhid, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $blockedDates = [];

    foreach ($results as $row) {
        $start = new DateTime($row['FromDate']);
        $end = new DateTime($row['ToDate']);
        while ($start <= $end) {
            $blockedDates[] = $start->format('Y-m-d');
            $start->modify('+1 day');
        }
    }

    echo json_encode($blockedDates);
}
?>
