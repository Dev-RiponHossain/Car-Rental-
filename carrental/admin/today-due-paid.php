<?php
session_start();
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

$date = date('Y-m-d');

$sql = "SELECT
            l.booking_number,
            u.FullName AS customer_name,
            b.mobile_number,
            v.VehiclesTitle AS car_name,
            l.due_paid_amount,
            l.payment_date
        FROM tbl_due_payment_logs l
        JOIN tblbooking b ON l.booking_number = b.BookingNumber
        JOIN tblusers u ON b.userEmail = u.EmailId
        JOIN tblvehicles v ON b.VehicleId = v.id
        WHERE l.payment_date = CURDATE()
        ORDER BY l.id DESC";

$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Today's Due Payments</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h3 class="text-center mb-4">আজকের Due Payment রিপোর্ট (<?= $date ?>)</h3>
    <?php if (count($results) > 0): ?>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Booking No</th>
                <th>Customer</th>
                <th>Mobile</th>
                <th>Car</th>
                <th>Paid Amount</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $total = 0;
            foreach ($results as $row):
                $total += $row->due_paid_amount;
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlentities($row->booking_number) ?></td>
                <td><?= htmlentities($row->customer_name) ?></td>
                <td><?= htmlentities($row->mobile_number) ?></td>
                <td><?= htmlentities($row->car_name) ?></td>
                <td><?= number_format($row->due_paid_amount, 2) ?> tk</td>
                <td><?= $row->payment_date ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">মোট সংগ্রহ:</th>
                <th colspan="2"><?= number_format($total, 2) ?> tk</th>
            </tr>
        </tfoot>
    </table>
    <?php else: ?>
        <div class="alert alert-warning text-center">আজকে কোনো due payment হয়নি।</div>
    <?php endif; ?>
</div>
</body>
</html>
