<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
    exit;
}

$month = date('Y-m'); // current month YYYY-MM, e.g. 2025-07

// Booking payments query (paid amounts and original payment method)
$sql = "SELECT
    b.BookingNumber,
    v.VehiclesTitle AS car_name,
    u.FullName AS customer_name,
    b.mobile_number,
    b.FromDate,
    b.ToDate,
    DATEDIFF(b.ToDate, b.FromDate) + 1 AS total_days,
    v.PricePerDay,
    (DATEDIFF(b.ToDate, b.FromDate) + 1) * v.PricePerDay AS total_rent,
    b.payment_method,
    b.payment_amount,
    b.payment_status
FROM tblbooking b
JOIN tblvehicles v ON b.VehicleId = v.id
JOIN tblusers u ON b.userEmail = u.EmailId
WHERE DATE_FORMAT(b.PostingDate, '%Y-%m') = :month
AND b.Status = 1
AND b.payment_amount > 0
ORDER BY b.PostingDate DESC";

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':month', $month);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_OBJ);

// Due payments query (payments from due logs)
$sql_due = "SELECT 
    d.booking_number,
    u.FullName AS customer_name,
    b.mobile_number,
    d.due_paid_amount,
    d.payment_method,
    d.payment_date
FROM tbl_due_payment_logs d
JOIN tblbooking b ON b.BookingNumber = d.booking_number
JOIN tblusers u ON b.userEmail = u.EmailId
WHERE DATE_FORMAT(d.payment_date, '%Y-%m') = :month
ORDER BY d.payment_date DESC";

$stmt_due = $dbh->prepare($sql_due);
$stmt_due->bindParam(':month', $month);
$stmt_due->execute();
$due_results = $stmt_due->fetchAll(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Booking Report</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include('includes/header.php'); ?>
<div class="ts-main-content">
<?php include('includes/leftbar.php'); ?>
<div class="content-wrapper">
<div class="container-fluid mt-4">

    <h2 class="page-title text-center mb-4">Monthly Booking Report (<?= date('F Y') ?>)</h2>

    <!-- Booking Paid Table -->
    <div class="panel panel-default">
        <div class="panel-heading"><h4 style="font-weight: bold;">Paid & Partially Paid Bookings This Month</h4></div>
        <div class="panel-body">
        <?php if (count($results) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
   <thead>
    <tr>
        <th>#</th>
        <th>Car</th>
        <th>Customer</th>
        <th>Mobile</th>
        <th>From</th>
        <th>To</th>
        <th>Days</th>
        <th>Rent/Day</th>
        <th>Payment Method</th>
        <th>Status</th>
        <th>Total Rent</th>
        <th>Paid</th>
    </tr>
</thead>
<tbody>
    <?php
    $cnt = 1;
    $booking_total = 0;
    foreach ($results as $row):
        $booking_total += $row->payment_amount;
    ?>
    <tr>
        <td><?= $cnt++ ?></td>
        <td><?= htmlentities($row->car_name) ?></td>
        <td><?= htmlentities($row->customer_name) ?></td>
        <td><?= htmlentities($row->mobile_number) ?></td>
        <td><?= htmlentities($row->FromDate) ?></td>
        <td><?= htmlentities($row->ToDate) ?></td>
        <td><?= $row->total_days ?></td>
        <td><?= number_format($row->PricePerDay, 2) ?> tk</td>
        <td><?= htmlentities($row->payment_method) ?></td>
        <td>
            <?= ($row->payment_status == 'Paid') 
                ? "<span style='color:green;font-weight:bold;'> Paid</span>" 
                : "<span style='color:red;font-weight:bold;'>Partial Paid</span>"; ?>
        </td>
        <td><?= number_format($row->total_rent, 2) ?> tk</td>
        <td><strong><?= number_format($row->payment_amount, 2) ?> tk</strong></td>
    </tr>
    <?php endforeach; ?>
</tbody>

</tbody>

                    <tfoot>
                        <tr>
                            <th colspan="11" class="text-right">Total Booking Paid:</th>
                            <th colspan="3"><strong><?= number_format($booking_total, 2) ?> tk</strong></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">No booking payments this month.</div>
        <?php endif; ?>
        </div>
    </div>

    <!-- Due Paid Table -->
    <div class="panel panel-default mt-5">
        <div class="panel-heading"><h4 style="font-weight: bold;">Due Payments Received This Month</h4></div>
        <div class="panel-body">
        <?php if (count($due_results) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Booking No</th>
                            <th>Customer</th>
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
                            <td><?= $cnt++ ?></td>
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
                            <th colspan="6" class="text-right">Total Due Paid:</th>
                            <th colspan="3"><strong><?= number_format($due_total, 2) ?> tk</strong></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No due payments received this month.</div>
        <?php endif; ?>
        </div>
    </div>

    <!-- Total Collection -->
    <?php if ((count($results) > 0 || count($due_results) > 0)): ?>
    <div class="text-right mt-4 mb-4 pr-4">
        <h4>Total Collected This Month (Booking + Due): 
            <strong><?= number_format(($booking_total ?? 0) + ($due_total ?? 0), 2) ?> tk</strong>
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
