<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0) { 
  header('location:index.php');
} else {
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<title>Car Rental Portal - My Booking</title>
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<link href="assets/css/slick.css" rel="stylesheet">
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" id="switcher-css" href="assets/switcher/css/switcher.css" media="all" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900">
<style>
.booking-table { width: 100%; margin: 20px 0; border-collapse: collapse; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
.booking-table th { background-color: #007bff; color: white; text-align: center; padding: 12px; font-weight: 600; }
.booking-table td { padding: 12px; text-align: center; vertical-align: middle; border-bottom: 1px solid #e9ecef; }
.booking-table tr:hover { background-color: #f8f9fa; }
.car-image { max-width: 100px; height: auto; display: block; margin: 0 auto 5px; border-radius: 4px; }
.car-name { color: #007bff; font-weight: 500; }
.action-cell { display: flex; justify-content: center; gap: 5px; flex-wrap: wrap; }
.btn-action { padding: 5px 10px; font-size: 13px; border-radius: 4px; min-width: 70px; }
.btn-print { background-color: #17a2b8; border-color: #17a2b8; color: white; }
.btn-confirm { background-color: #28a745; border-color: #28a745; color: white; }
.btn-cancel { background-color: #dc3545; border-color: #dc3545; color: white; }
.status-badge { padding: 5px 10px; border-radius: 20px; font-size: 13px; font-weight: 500; display: inline-block; }
.badge-success { background-color: #28a745; color: white; }
.badge-danger { background-color: #dc3545; color: white; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-info { background-color: #17a2b8; color: white; }
.text-muted { color: #6c757d; }
.price { font-weight: 600; color: #28a745; }
.days { font-weight: 500; color: #6c757d; }
</style>
</head>
<body>

<?php include('includes/colorswitcher.php'); ?>
<?php include('includes/header.php'); ?> 

<section class="page-header profile_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1>My Booking</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="#">Home</a></li>
        <li>My Booking</li>
      </ul>
    </div>
  </div>
  <div class="dark-overlay"></div>
</section>

<?php 
$useremail = $_SESSION['login'];
$sql = "SELECT * FROM tblusers WHERE EmailId = :useremail";
$query = $dbh->prepare($sql);
$query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0) {
  foreach($results as $result) {
?>
<section class="user_profile inner_pages">
  <div class="container">
    <div class="user_profile_info gray-bg padding_4x4_40">
      <div class="upload_user_logo">
        <img src="assets/images/dealer-logo.jpg" alt="image">
      </div>
      <div class="dealer_info">
        <h5><?php echo htmlentities($result->FullName); ?></h5>
        <p><?php echo htmlentities($result->Address); ?><br>
          <?php echo htmlentities($result->City); ?>&nbsp;<?php echo htmlentities($result->Country); ?>
        </p>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="profile_wrap">
          <h5 class="text-center mb-4">My All Bookings</h5>
          <div class="table-responsive">
            <table class="booking-table">
              <thead>
                <tr>
                  <th>Booking No</th>
                  <th>Car Details</th>
                  <th>Destination</th>
                  <th>Rental Period</th>
                  <th>Days</th>
                  <th>Price/Day</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Payment</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
<?php 
$sql = "SELECT 
    tblvehicles.VehiclesTitle,
    tblvehicles.id as vid,
    tblvehicles.Vimage1,
    tblbrands.BrandName,
    tblbooking.FromDate,
    tblbooking.ToDate,
    tblbooking.message,
    tblbooking.Status,
    tblvehicles.PricePerDay,
    DATEDIFF(tblbooking.ToDate, tblbooking.FromDate) as totaldays,
    tblbooking.BookingNumber,
    tblbooking.id as bookingId,
    tblbooking.payment_status,
    tblbooking.address,
    tblbooking.district_name,
    tblbooking.division_name
FROM tblbooking 
JOIN tblvehicles ON tblbooking.VehicleId = tblvehicles.id 
JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand 
WHERE tblbooking.userEmail = :useremail 
ORDER BY tblbooking.id DESC";

$query = $dbh->prepare($sql);
$query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
$query->execute();
$bookings = $query->fetchAll(PDO::FETCH_OBJ);

if($query->rowCount() > 0) {
  foreach($bookings as $booking) {
    $tds = $booking->totaldays + 1;
    $ppd = $booking->PricePerDay;
    $total = $tds * $ppd;

    if ($booking->Status == 1) {
        $status = "<span class='status-badge badge-success'>Confirmed</span>";
    } elseif ($booking->Status == 2) {
        $status = "<span class='status-badge badge-danger'>Cancelled</span>";
    } else {
        $status = "<span class='status-badge badge-warning'>Pending</span>";
    }

    $dueAmountText = "";
    if ($booking->payment_status == 'Due') {
        $dueQuery = $dbh->prepare("SELECT due_amount FROM tbl_due_payments WHERE booking_number = :booking ORDER BY id DESC LIMIT 1");
        $dueQuery->bindParam(':booking', $booking->BookingNumber, PDO::PARAM_STR);
        $dueQuery->execute();
        $dueResult = $dueQuery->fetch(PDO::FETCH_OBJ);
        if ($dueResult) {
            $dueAmountText = " (Due: ".$dueResult->due_amount."tk)";
        }
    }

    if ($booking->Status == 2) {
        $paymentStatus = "<span class='status-badge badge-danger'>N/A</span>";
    } else {
        if ($booking->payment_status == 'Paid') {
            $paymentStatus = "<span class='status-badge badge-success'>💰 Paid</span>";
        } elseif ($booking->payment_status == 'Due') {
            $paymentStatus = "<span class='btn btn-action btn-confirm'><i class='fa fa-money'></i> Due</span>";

        } else {
            $paymentStatus = "<a href='payment.php?booking=".urlencode($booking->BookingNumber)."' class='status-badge badge-info' style='color:white; text-decoration:none;'>Pending</a>";
        }
    }

    // Actions column WITHOUT "Pay Due"
    if ($booking->Status == 1 && ($booking->payment_status == 'Paid' || $booking->payment_status == 'Due')) {
        $actions = "<div class='action-cell'>";
        $actions .= "<a href='print-invoice.php?booking=".urlencode($booking->BookingNumber)."' class='btn btn-action btn-print' target='_blank'><i class='fa fa-print'></i> Print</a></div>";
    } elseif ($booking->Status == 0) {
        $actions = "<span class='text-muted'>Pending Approval</span>";
    } else {
        $actions = "<span class='text-muted'>N/A</span>";
    }

    echo "<tr>
      <td>".htmlentities($booking->BookingNumber)."</td>
      <td>
        <img src='admin/img/vehicleimages/".htmlentities($booking->Vimage1)."' class='car-image'>
        <a href='vehical-details.php?vhid=".htmlentities($booking->vid)."' target='_blank' class='car-name'>
          ".htmlentities($booking->BrandName)." ".htmlentities($booking->VehiclesTitle)."
        </a>
      </td>
      <td>" . htmlentities($booking->address) . ", " . htmlentities($booking->district_name) . ", " . htmlentities($booking->division_name) . "</td>
      <td>
        <div>".htmlentities($booking->FromDate)."</div>
        <div>to</div>
        <div>".htmlentities($booking->ToDate)."</div>
      </td>
      <td class='days'>".htmlentities($tds)."</td>
      <td class='price'>".htmlentities($ppd)."tk</td>
      <td class='price'>".htmlentities($total)."tk</td>
      <td>$status</td>
      <td>$paymentStatus</td>
      <td>$actions</td>
    </tr>";
  }
} else {
  echo "<tr><td colspan='10' class='text-center'>No bookings found.</td></tr>";
}
?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php }} ?>

<?php include('includes/footer.php'); ?>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/slick.min.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script>
<script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>