<?php
session_start();
include('includes/config.php');

if (!isset($_GET['booking'])) {
    die("Invalid access!");
}

$bookingNo = $_GET['booking'];

// Booking Details Query
$sql = "SELECT 
    tblusers.FullName, tblusers.EmailId, tblusers.ContactNo,
    tblvehicles.VehiclesTitle, tblvehicles.PricePerDay,
    tblbrands.BrandName,
    tblbooking.BookingNumber, tblbooking.FromDate, tblbooking.ToDate,
    tblbooking.address, tblbooking.district_name, tblbooking.division_name,
    tblbooking.payment_status,
    DATEDIFF(tblbooking.ToDate, tblbooking.FromDate) + 1 as totaldays
FROM tblbooking
JOIN tblvehicles ON tblbooking.VehicleId = tblvehicles.id
JOIN tblbrands ON tblvehicles.VehiclesBrand = tblbrands.id
JOIN tblusers ON tblbooking.userEmail = tblusers.EmailId
WHERE tblbooking.BookingNumber = :bookingNo";

$query = $dbh->prepare($sql);
$query->bindParam(':bookingNo', $bookingNo, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);

if (!$result) {
    die("No booking found!");
}

// Account calculation
$rent = $result->PricePerDay;
$total = $rent * $result->totaldays;

// Check for due if payment_status is "Due"
$dueAmount = 0;
if ($result->payment_status == 'Due') {
    $dueQuery = $dbh->prepare("SELECT due_amount FROM tbl_due_payments WHERE booking_number = :bookingNo ORDER BY id DESC LIMIT 1");
    $dueQuery->bindParam(':bookingNo', $bookingNo, PDO::PARAM_STR);
    $dueQuery->execute();
    $dueResult = $dueQuery->fetch(PDO::FETCH_OBJ);
    if ($dueResult) {
        $dueAmount = $dueResult->due_amount;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - Booking #<?= htmlentities($result->BookingNumber) ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body { padding: 30px; font-family: Arial, sans-serif; background-color: #f8f9fa; }
        .invoice-box { background: #fff; border: 1px solid #ddd; padding: 30px; max-width: 1000px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .invoice-box h2 { margin-bottom: 30px; color: #007bff; }
        .btn-print { margin-top: 20px; }
        .booking-info { margin-bottom: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 5px; }
        .info-item { margin-bottom: 10px; }
        .info-label { font-weight: bold; color: #495057; }
        .info-value { color: #212529; }
        th { background-color: #f2f2f2; text-align: center; }
        td { text-align: center; }
        @media print {
            body { padding: 0; background: none; }
            .invoice-box { border: none; box-shadow: none; padding: 0; max-width: 100%; }
            .btn-print { display: none; }
            .booking-info { background-color: transparent !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            table { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <h2 class="text-center">Car Rental Invoice</h2>

    <div class="booking-info">
        <div class="row">
            <div class="col-md-4">
                <div class="info-item">
                    <span class="info-label">Booking No:</span>
                    <span class="info-value"><?= htmlentities($result->BookingNumber) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Customer:</span>
                    <span class="info-value"><?= htmlentities($result->FullName) ?></span>
                </div><br>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlentities($result->EmailId) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Contact:</span>
                    <span class="info-value"><?= htmlentities($result->ContactNo) ?></span>
                </div>
            </div>  
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Vehicle</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Destination</th>
                <th>Rent/Day</th>
                <th>Total Days</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= htmlentities($result->BrandName) ?> - <?= htmlentities($result->VehiclesTitle) ?></td>
                <td><?= htmlentities($result->FromDate) ?></td>
                <td><?= htmlentities($result->ToDate) ?></td>
                <td><?= htmlentities($result->address) . ', ' . htmlentities($result->district_name) . ', ' . htmlentities($result->division_name) ?></td>
                <td><?= htmlentities($rent) ?>tk</td>
                <td><?= htmlentities($result->totaldays) ?></td>
                <td><strong><?= htmlentities($total) ?>tk</strong></td>
            </tr>
            <tr>
                <td colspan="7" class="text-end">
                <?php if ($result->payment_status == 'Paid') : ?>
                    <span class="badge bg-success" style="font-size: 16px;">✅ Full Paid</span>
                <?php elseif ($result->payment_status == 'Due') : ?>
                    <span class="badge bg-warning text-dark" style="font-size: 16px;">⏳Due: <?= htmlentities($dueAmount) ?>tk</span>
                <?php else : ?>
                    <span class="badge bg-secondary" style="font-size: 16px;">Pending Payment</span>
                <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="text-center">
        <button class="btn btn-primary btn-print" onclick="window.print()">🖨️ Print Invoice</button>
    </div>
</div>
</body>
</html>
