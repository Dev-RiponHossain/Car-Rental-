<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
    exit;
}

$sql = "SELECT
    b.BookingNumber,
    v.VehiclesTitle AS car_name,
    u.FullName AS customer_name,
    u.EmailId AS customer_email,
    b.mobile_number,
    b.FromDate,
    b.ToDate,
    DATEDIFF(b.ToDate, b.FromDate) + 1 AS total_days,
    v.PricePerDay,
    ((DATEDIFF(b.ToDate, b.FromDate) + 1) * v.PricePerDay) AS total_rent,
    COALESCE(dp.paid_amount, b.payment_amount) AS paid_amount,
    CONCAT_WS(', ',
        b.payment_method,
        (SELECT GROUP_CONCAT(DISTINCT l.payment_method SEPARATOR ', ') FROM tbl_due_payment_logs l WHERE l.booking_number = b.BookingNumber)
    ) AS payment_method_combined
FROM tblbooking b
JOIN tblvehicles v ON b.VehicleId = v.id
JOIN tblusers u ON b.userEmail = u.EmailId
LEFT JOIN tbl_due_payments dp ON dp.booking_number = b.BookingNumber
WHERE b.Status = 1
  AND (dp.paid_amount > 0 OR (dp.paid_amount IS NULL AND b.payment_status = 'Paid'))
ORDER BY u.FullName ASC, b.PostingDate DESC";

$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

$grouped = [];
foreach ($results as $row) {
    $grouped[$row->customer_email][] = $row;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All-Time Paid Booking Report</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="refresh" content="60">
    <style>
        .content-wrapper { margin-left: 220px; padding-left: 10px; }
        .panel-heading { font-weight: bold; font-size: 17px; background-color: #f5f5f5; padding: 10px; }
        .panel-heading span { text-transform: none !important; color: black; }
        #searchContainer { text-align: right; margin-bottom: 20px; }
        #searchInput { width: 200px; font-size: 14px; padding: 6px 10px; display: inline-block; }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>
<div class="ts-main-content">
<?php include('includes/leftbar.php'); ?>
<div class="content-wrapper">
    <div class="container-fluid mt-4">
        <h2 class="page-title text-center mb-4">All-Time Paid Booking Report</h2>

        <div id="searchContainer">
            <input type="text" id="searchInput" class="form-control" placeholder="Search customer name or email...">
        </div>

        <div id="customerTables">
        <?php if (count($grouped) > 0): ?>
            <?php foreach ($grouped as $email => $bookings): ?>
                <?php
                $customer_name = $bookings[0]->customer_name;
                $sub_total = 0;
                ?>
                <div class="panel panel-default mb-4 customer-panel">
                    <div class="panel-heading">
                        <span style="font-size: 18px; font-weight: 700;">
                            Customer: <?= htmlentities($customer_name) ?>
                        </span>
                        &nbsp; | &nbsp;
                        <span style="font-size: 16px; font-weight: 700;">
                            Email: <?= htmlentities(strtolower($email)) ?>
                        </span>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Car Name</th>
                                        <th>Mobile</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Days</th>
                                        <th>Rent/Day (tk)</th>
                                        <th>Payment Method</th>
                                        <th>Total Rent</th>
                                        <th>Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $cnt = 1; foreach ($bookings as $row): $sub_total += $row->paid_amount; ?>
                                    <tr>
                                        <td><?= $cnt++ ?></td>
                                        <td><?= htmlentities($row->car_name) ?></td>
                                        <td><?= htmlentities($row->mobile_number) ?></td>
                                        <td><?= htmlentities($row->FromDate) ?></td>
                                        <td><?= htmlentities($row->ToDate) ?></td>
                                        <td><?= $row->total_days ?></td>
                                        <td><?= number_format($row->PricePerDay, 2) ?> tk</td>
                                        <td><?= htmlentities($row->payment_method_combined ?? 'N/A') ?></td>
                                        <td><?= number_format($row->total_rent, 2) ?> tk</td>
                                        <td><strong><?= number_format($row->paid_amount, 2) ?> tk</strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="9" class="text-right">Total Paid by <?= htmlentities($customer_name) ?>:</th>
                                        <th><?= number_format($sub_total, 2) ?> tk</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning text-center">No paid bookings found.</div>
        <?php endif; ?>
        </div>
    </div>
</div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
  $(document).ready(function(){
    $('#searchInput').on('keyup', function() {
      var value = $(this).val().toLowerCase();
      $('.customer-panel').each(function() {
        var headerText = $(this).find('.panel-heading').text().toLowerCase();
        $(this).toggle(headerText.includes(value));
      });
    });
  });
</script>
</body>
</html>
