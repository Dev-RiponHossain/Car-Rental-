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
  AND b.ToDate >= CURDATE()
ORDER BY v.VehiclesTitle ASC, b.PostingDate DESC";

$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

// Group by car name
$grouped_by_car = [];
foreach ($results as $row) {
    $grouped_by_car[$row->car_name][] = $row;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car-wise Paid Booking Report</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="refresh" content="60">
    <style>
        .content-wrapper { margin-left: 220px; padding-left: 10px; }
        .panel-heading { font-weight: bold; font-size: 17px; background-color: #f5f5f5; padding: 10px; color: black; }
        #searchContainer { text-align: right; margin-bottom: 20px; }
        #searchInput { 
            width: 200px; 
            font-size: 14px; 
            padding: 6px 10px; 
            display: inline-block; 
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #searchInput:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0,123,255,.5);
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>
<div class="ts-main-content">
<?php include('includes/leftbar.php'); ?>
<div class="content-wrapper">
    <div class="container-fluid mt-4">
        <h2 class="page-title text-center mb-4">Car-wise Paid Booking Report</h2>

        <div id="searchContainer">
            <input type="text" id="searchInput" class="form-control" placeholder="Search car name...">
        </div>

        <div id="carTables">
        <?php if (count($grouped_by_car) > 0): ?>
            <?php foreach ($grouped_by_car as $car_name => $bookings): ?>
                <?php $car_total = 0; ?>
                <div class="panel panel-default mb-4 car-panel">
                    <div class="panel-heading"><h4 style="font-weight: bold;"><?= htmlentities($car_name) ?></h4></div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Days</th>
                                        <th>Rent/Day (tk)</th>
                                        <th>Payment Method</th>
                                        <th>Total Rent (tk)</th>
                                        <th>Paid (tk)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $cnt = 1; foreach ($bookings as $row): $car_total += $row->paid_amount; ?>
                                    <tr>
                                        <td><?= $cnt++ ?></td>
                                        <td><?= htmlentities($row->customer_name) ?></td>
                                        <td><?= htmlentities($row->customer_email) ?></td>
                                        <td><?= htmlentities($row->mobile_number) ?></td>
                                        <td><?= htmlentities($row->FromDate) ?></td>
                                        <td><?= htmlentities($row->ToDate) ?></td>
                                        <td><?= $row->total_days ?></td>
                                        <td><?= number_format($row->PricePerDay, 2) ?></td>
                                        <td><?= htmlentities($row->payment_method_combined ?? 'N/A') ?></td>
                                        <td><?= number_format($row->total_rent, 2) ?></td>
                                        <td><strong><?= number_format($row->paid_amount, 2) ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="10" class="text-right">Total Paid for <?= htmlentities($car_name) ?>:</th>
                                        <th><?= number_format($car_total, 2) ?></th>
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
      $('.car-panel').each(function() {
        var headerText = $(this).find('.panel-heading').text().toLowerCase();
        $(this).toggle(headerText.includes(value));
      });
    });
  });
</script>
</body>
</html>
