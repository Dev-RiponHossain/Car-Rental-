<?php
session_start();
/*error_reporting(0);*/
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
    exit;
}

$date = date('Y-m-d');

// Main booking payments query (paid and partially paid)
$sql = "SELECT
    b.BookingNumber AS BookingNumber,
    v.VehiclesTitle AS car_name,
    u.FullName AS customer_name,
    u.EmailId AS customer_email,
    b.mobile_number,
    b.FromDate,
    b.ToDate,
    DATEDIFF(b.ToDate, b.FromDate) + 1 AS total_days,
    v.PricePerDay,
    (DATEDIFF(b.ToDate, b.FromDate) + 1) * v.PricePerDay AS total_rent,
    b.payment_amount AS paid_amount,
    COALESCE(dp.due_amount, 0) AS due_amount,
    COALESCE(dp.total_amount, (DATEDIFF(b.ToDate, b.FromDate) + 1) * v.PricePerDay) AS total_amount,
    b.payment_method AS payment_method
FROM tblbooking b
JOIN tblvehicles v ON b.VehicleId = v.id
JOIN tblusers u ON b.userEmail = u.EmailId
LEFT JOIN (
    SELECT dp1.*
    FROM tbl_due_payments dp1
    INNER JOIN (
        SELECT booking_number, MAX(id) AS max_id
        FROM tbl_due_payments
        GROUP BY booking_number
    ) dp2 ON dp1.booking_number = dp2.booking_number AND dp1.id = dp2.max_id
) dp ON dp.booking_number = b.BookingNumber
WHERE DATE(b.PostingDate) = :today
AND b.Status = 1
ORDER BY b.PostingDate DESC";

// Execute booking query
$query = $dbh->prepare($sql);
$query->bindParam(':today', $date);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

// Due payment logs query
$sql_due = "SELECT 
    d.booking_number,
    u.FullName AS customer_name,
    b.mobile_number,
    d.due_paid_amount,
    d.payment_date,
    d.payment_method
FROM tbl_due_payment_logs d
JOIN tblbooking b ON b.BookingNumber = d.booking_number
JOIN tblusers u ON u.EmailId = b.userEmail
WHERE d.payment_date = :today";

$stmt_due = $dbh->prepare($sql_due);
$stmt_due->bindParam(':today', $date);
$stmt_due->execute();
$due_results = $stmt_due->fetchAll(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <title>Today's Booking Report</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="refresh" content="30">
</head>
<body>
<?php include('includes/header.php'); ?>
<div class="ts-main-content">
    <?php include('includes/leftbar.php'); ?>
    <div class="content-wrapper">
        <div class="container-fluid mt-4">

            <h2 class="page-title text-center mb-4">Today's Booking Report (<?= htmlentities($date) ?>)</h2>

            <!-- Booking Payments Table -->
            <div class="panel panel-default">
                <div class="panel-heading"><h4 style="font-weight: bold;">Paid & Partially Paid Bookings</h4></div>
                <div class="panel-body">
                    <?php if (count($results) > 0): ?>
                    <div class="table-responsive">
                        <table id="zctb" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Car Name</th>
                                    <th>Customer Name</th>
                                    <th>Mobile</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Total Days</th>
                                    <th>Rent/Day</th>
                                    <th>Payment Method</th>
                                    <th>Payment Status</th>
                                    <th>Total Rent</th>
                                    <th>Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cnt = 1;
                                $grand_total = 0;
                                foreach ($results as $row):
                                    $grand_total += $row->paid_amount;
                                ?>
                                <tr>
                                    <td><?= $cnt++; ?></td>
                                    <td><?= htmlentities($row->car_name) ?></td>
                                    <td><?= htmlentities($row->customer_name) ?></td>
                                    <td><?= htmlentities($row->mobile_number) ?></td>
                                    <td><?= htmlentities($row->FromDate) ?></td>
                                    <td><?= htmlentities($row->ToDate) ?></td>
                                    <td><?= $row->total_days ?></td>
                                    <td><?= number_format($row->PricePerDay, 2) ?> tk</td>
                                    <td><?= htmlentities($row->payment_method ?? 'N/A') ?></td>
                                    <td>
                                        <?php 
                                        if ($row->due_amount > 0) {
                                            echo "<span style='color:red;font-weight:bold;'>Partial Paid</span>";
                                        } else {
                                            echo "<span style='color:green;font-weight:bold;'> Paid</span>";
                                        }
                                        ?>
                                    </td>
                                    <td><?= number_format($row->total_rent, 2) ?> tk</td>
                                    <td><strong><?= number_format($row->paid_amount, 2) ?> tk</strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="11" class="text-right">Total Collected Today:</th>
                                    <th><strong><?= number_format($grand_total, 2) ?> tk</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">No payments made today.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Due Payments Logs Table -->
            <div class="panel panel-default mt-5">
                <div class="panel-heading"><h4 style="font-weight: bold;">Due Payments Received Today</h4></div>
                <div class="panel-body">
                    <?php if (count($due_results) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Booking No</th>
                                    <th>Customer Name</th>
                                    <th>Mobile</th>
                                    <th>Payment Date</th>
                                    <th>Payment Method</th>
                                    <th>Paid Amount</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cnt = 1;
                                $due_total = 0;
                                foreach ($due_results as $row):
                                    $due_total += $row->due_paid_amount;
                                ?>
                                <tr>
                                    <td><?= $cnt++; ?></td>
                                    <td><?= htmlentities($row->booking_number) ?></td>
                                    <td><?= htmlentities($row->customer_name) ?></td>
                                    <td><?= htmlentities($row->mobile_number) ?></td>
                                    <td><?= htmlentities($row->payment_date) ?></td>
                                    <td><?= htmlentities($row->payment_method) ?></td>
                                    <td><strong><?= number_format($row->due_paid_amount, 2) ?> tk</strong></td>
                                    
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-right">Total Due Collected Today:</th>
                                    <th colspan="2"><strong><?= number_format($due_total, 2) ?> tk</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">No due payments received today.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Total Combined Collection -->
            <?php if ((count($results) > 0 || count($due_results) > 0)): ?>
            <div class="text-right mt-3 mb-4 pr-4">
                <h4>Total Collected (Booking + Due): 
                    <strong><?= number_format(($grand_total ?? 0) + ($due_total ?? 0), 2) ?> tk</strong>
                </h4>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>
