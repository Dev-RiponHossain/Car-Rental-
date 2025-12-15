<?php
session_start();
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {   
  header('location:index.php'); 
  exit; 
}

$filter_from = $_POST['from_date'] ?? ''; 
$filter_to = $_POST['to_date'] ?? '';
$report_type = $_POST['report_type'] ?? 'booking';

$results = [];
$groupedData = [];

// Function to get grouped summary for each report type
function getGroupedSummary($dbh, $report_type) {
    if ($report_type == 'booking') {
        $sql = "SELECT
                    b.BookingNumber,
                    b.payment_amount AS paid_amount,
                    DATE_FORMAT(b.PostingDate, '%Y-%m') AS month_group
                FROM tblbooking b
                WHERE b.Status = 1 AND b.payment_amount > 0
                ORDER BY b.PostingDate DESC";
    } else {
        $sql = "SELECT
                    l.booking_number,
                    l.due_paid_amount AS paid_amount,
                    DATE_FORMAT(l.payment_date, '%Y-%m') AS month_group
                FROM tbl_due_payment_logs l
                WHERE l.due_paid_amount > 0
                ORDER BY l.payment_date DESC";
    }

    $query = $dbh->prepare($sql);
    $query->execute();
    $allData = $query->fetchAll(PDO::FETCH_OBJ);

    $grouped = [];
    foreach ($allData as $row) {
        $grouped[$row->month_group][] = $row;
    }
    return $grouped;
}

// If filter is set, get filtered data for selected report_type
if (!empty($filter_from) && !empty($filter_to)) {
    if ($report_type == 'booking') {
        $sql = "SELECT
                    b.BookingNumber,
                    v.VehiclesTitle AS car_name, 
                    u.FullName AS customer_name, 
                    b.mobile_number, 
                    b.FromDate, 
                    b.ToDate, 
                    b.PostingDate AS PostingDate, 
                    DATEDIFF(b.ToDate, b.FromDate) + 1 AS total_days, 
                    v.PricePerDay, 
                    ((DATEDIFF(b.ToDate, b.FromDate) + 1) * v.PricePerDay) AS total_rent, 
                    b.payment_amount AS paid_amount,
                    b.payment_method
                FROM tblbooking b 
                JOIN tblvehicles v ON b.VehicleId = v.id 
                JOIN tblusers u ON b.userEmail = u.EmailId 
                WHERE b.Status = 1 AND b.payment_amount > 0
                  AND DATE(b.PostingDate) BETWEEN :from AND :to
                ORDER BY b.PostingDate DESC";
    } else {
        $sql = "SELECT
                    b.BookingNumber,
                    v.VehiclesTitle AS car_name, 
                    u.FullName AS customer_name, 
                    b.mobile_number, 
                    b.FromDate, 
                    b.ToDate, 
                    l.payment_date AS PostingDate,
                    DATEDIFF(b.ToDate, b.FromDate) + 1 AS total_days, 
                    v.PricePerDay, 
                    ((DATEDIFF(b.ToDate, b.FromDate) + 1) * v.PricePerDay) AS total_rent, 
                    l.due_paid_amount AS paid_amount,
                    l.payment_method
                FROM tbl_due_payment_logs l
                JOIN tblbooking b ON b.BookingNumber = l.booking_number
                JOIN tblvehicles v ON b.VehicleId = v.id 
                JOIN tblusers u ON b.userEmail = u.EmailId 
                WHERE l.due_paid_amount > 0
                  AND DATE(l.payment_date) BETWEEN :from AND :to
                ORDER BY l.payment_date DESC";
    }

    $query = $dbh->prepare($sql);
    $query->bindParam(':from', $filter_from, PDO::PARAM_STR);
    $query->bindParam(':to', $filter_to, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
} else {
    // No filter: get grouped summary for both report types
    $groupedData['booking'] = getGroupedSummary($dbh, 'booking');
    $groupedData['due'] = getGroupedSummary($dbh, 'due');
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Report</title>
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
      <h2 class="page-title text-center mb-4">Booking Report</h2>

      <form method="post" class="row mb-4">
        <div class="col-md-3">
          <label>From Date:</label>
          <input type="date" name="from_date" value="<?= htmlentities($filter_from) ?>" class="form-control">
        </div>
        <div class="col-md-3">
          <label>To Date:</label>
          <input type="date" name="to_date" value="<?= htmlentities($filter_to) ?>" class="form-control">
        </div>
        <div class="col-md-3">
          <label>Report Type:</label>
          <select name="report_type" class="form-control">
            <option value="booking" <?= $report_type == 'booking' ? 'selected' : '' ?>>Booking Partial Payments</option>
            <option value="due" <?= $report_type == 'due' ? 'selected' : '' ?>>Paid Due Payments</option>
          </select>
        </div>
        <div class="col-md-3">
          <label>&nbsp;</label>
          <button type="submit" class="btn btn-primary form-control">Filter</button>
        </div>
      </form>

<?php if (!empty($filter_from) && !empty($filter_to)): ?>
  <?php if (!empty($results)): ?>
    <h4 class="text-center mt-4">
      <?= ucfirst($report_type) ?> Report from <?= htmlentities($filter_from) ?> to <?= htmlentities($filter_to) ?>
    </h4>
    <div class="table-responsive mb-5">
      <table class="table table-bordered table-striped" id="filteredTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Car</th>
            <th>Customer</th>
            <th>Mobile</th>
            <th>Date</th>
            <th>From</th>
            <th>To</th>
            <th>Days</th>
            <th>Rent/Day</th>
            <th>Method</th>
            <th>Total</th>
            <th>Paid</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $cnt = 1; 
          $filter_total = 0; 
          foreach ($results as $row): 
            $filter_total += $row->paid_amount; 
          ?>
          <tr>
            <td><?= $cnt++ ?></td>
            <td><?= htmlentities($row->car_name) ?></td>
            <td><?= htmlentities($row->customer_name) ?></td>
            <td><?= htmlentities($row->mobile_number) ?></td>
            <td><?= date('d M Y h:i A', strtotime($row->PostingDate)) ?></td>
            <td><?= htmlentities($row->FromDate) ?></td>
            <td><?= htmlentities($row->ToDate) ?></td>
            <td><?= $row->total_days ?></td>
            <td><?= number_format($row->PricePerDay, 2) ?> tk</td>
            <td><?= htmlentities($row->payment_method ?? 'N/A') ?></td>
            <td><?= number_format($row->total_rent, 2) ?> tk</td>
            <td><strong><?= number_format($row->paid_amount, 2) ?> tk</strong></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="11" class="text-right">Total Paid:</th>
            <th><strong><?= number_format($filter_total, 2) ?> tk</strong></th>
          </tr>
        </tfoot>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center">⚠️ No data found for selected date range.</div>
  <?php endif; ?>

<?php else: ?>
  <!-- Show summaries for both report types side by side -->
  <div class="row">
    <div class="col-md-6">
      <h4 class="text-center mt-4">Monthly Booking Partial Payments Summary</h4>
      <?php if (!empty($groupedData['booking'])): ?>
      <div class="table-responsive mb-5">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Month</th>
              <th>Total Bookings</th>
              <th>Total Paid</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $cnt = 1; 
            $grand_total = 0;
            foreach ($groupedData['booking'] as $month => $bookings):
              $month_total = 0;
              foreach ($bookings as $row) {
                $month_total += $row->paid_amount;
              }
              $grand_total += $month_total;
            ?>
            <tr>
              <td><?= $cnt++ ?></td>
              <td><?= date("F Y", strtotime($month)) ?></td>
              <td><?= count($bookings) ?> bookings</td>
              <td><strong><?= number_format($month_total, 2) ?> tk</strong></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-right">Grand Total Paid:</th>
              <th><strong><?= number_format($grand_total, 2) ?> tk</strong></th>
            </tr>
          </tfoot>
        </table>
      </div>
      <?php else: ?>
        <div class="alert alert-warning text-center">⚠️ No booking partial payment data found.</div>
      <?php endif; ?>
    </div>

    <div class="col-md-6">
      <h4 class="text-center mt-4">Monthly Paid Due Payments Summary</h4>
      <?php if (!empty($groupedData['due'])): ?>
      <div class="table-responsive mb-5">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Month</th>
              <th>Total Payments</th>
              <th>Total Paid</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $cnt = 1; 
            $grand_total = 0;
            foreach ($groupedData['due'] as $month => $payments):
              $month_total = 0;
              foreach ($payments as $row) {
                $month_total += $row->paid_amount;
              }
              $grand_total += $month_total;
            ?>
            <tr>
              <td><?= $cnt++ ?></td>
              <td><?= date("F Y", strtotime($month)) ?></td>
              <td><?= count($payments) ?> payments</td>
              <td><strong><?= number_format($month_total, 2) ?> tk</strong></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-right">Grand Total Paid:</th>
              <th><strong><?= number_format($grand_total, 2) ?> tk</strong></th>
            </tr>
          </tfoot>
        </table>
      </div>
      <?php else: ?>
        <div class="alert alert-warning text-center">⚠️ No paid due payment data found.</div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

    </div>
  </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script>
  $(document).ready(function(){
    $('#filteredTable').DataTable();
  });
</script>
</body>
</html>
