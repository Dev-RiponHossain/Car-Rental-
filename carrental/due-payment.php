<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['login'])) {
    header('location:index.php');
    exit;
}

$bookingNumber = $_GET['booking'] ?? '';
if (!$bookingNumber) {
    die("Invalid booking.");
}

// Fetch booking info from tblbooking
$sql = "SELECT BookingNumber, userEmail, payment_amount, payment_status, FromDate, ToDate, VehicleId 
        FROM tblbooking 
        WHERE BookingNumber = :bookingNumber AND payment_status IN ('Due', 'Pending')";
$query = $dbh->prepare($sql);
$query->bindParam(':bookingNumber', $bookingNumber, PDO::PARAM_INT);
$query->execute();
$booking = $query->fetch(PDO::FETCH_OBJ);

if (!$booking) {
    die("No due payment found or invalid booking number.");
}

// Fetch PricePerDay from tblvehicles
$sql2 = "SELECT PricePerDay FROM tblvehicles WHERE id = :vehicleId";
$stmt2 = $dbh->prepare($sql2);
$stmt2->bindParam(':vehicleId', $booking->VehicleId, PDO::PARAM_INT);
$stmt2->execute();
$vehicle = $stmt2->fetch(PDO::FETCH_OBJ);
if (!$vehicle) {
    die("Vehicle data not found.");
}

// Calculate total days and amount
$from = new DateTime($booking->FromDate);
$to = new DateTime($booking->ToDate);
$interval = $to->diff($from);
$totalDays = $interval->days + 1; // inclusive

$totalAmount = $totalDays * $vehicle->PricePerDay;
$paidAmount = floatval($booking->payment_amount ?? 0);
$dueAmount = $totalAmount - $paidAmount;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payNow = floatval($_POST['pay_now'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? '';

    // Validation
    if ($payNow <= 0 || $payNow != $dueAmount) {
        $error = "You must pay the exact due amount (" . number_format($dueAmount, 2) . " tk).";
    } elseif (!in_array($payment_method, ['bKash', 'Nagad', 'Card', 'Cash'])) {
        $error = "Invalid payment method.";
    } else {
        $newPaidAmount = $paidAmount + $payNow;
        $newStatus = ($newPaidAmount >= $totalAmount) ? 'Paid' : 'Due';

        // Update tblbooking
        $updateBooking = $dbh->prepare("UPDATE tblbooking 
            SET payment_amount = :paidAmount, 
                payment_status = :status, 
                payment_method = :paymentMethod 
            WHERE BookingNumber = :bookingNumber");
        $updateBooking->execute([
            ':paidAmount' => $newPaidAmount,
            ':status' => $newStatus,
            ':paymentMethod' => $payment_method,
            ':bookingNumber' => $bookingNumber
        ]);

        // Check if record exists in tbl_due_payments
        $checkDue = $dbh->prepare("SELECT id FROM tbl_due_payments WHERE booking_number = :bookingNumber");
        $checkDue->execute([':bookingNumber' => $bookingNumber]);

        if ($checkDue->rowCount() > 0) {
            // Fetch current due info
            $getCurrent = $dbh->prepare("SELECT paid_amount FROM tbl_due_payments WHERE booking_number = :bookingNumber");
            $getCurrent->execute([':bookingNumber' => $bookingNumber]);
            $current = $getCurrent->fetch(PDO::FETCH_OBJ);

            $updatedPaid = $current->paid_amount + $payNow;
            $updatedDue = $totalAmount - $updatedPaid;
            $newStatusDue = ($updatedDue <= 0) ? 'Paid' : 'Due';

            // Update existing record
            $updateDue = $dbh->prepare("UPDATE tbl_due_payments 
                SET paid_amount = :paidAmount, 
                    due_amount = :dueAmount, 
                    status = :status, 
                    payment_method = :paymentMethod, 
                    paid_date = NOW() 
                WHERE booking_number = :bookingNumber");
            $updateDue->execute([
                ':paidAmount' => $updatedPaid,
                ':dueAmount' => $updatedDue,
                ':status' => $newStatusDue,
                ':paymentMethod' => $payment_method,
                ':bookingNumber' => $bookingNumber
            ]);
        } else {
            // Insert new record
            $insertDue = $dbh->prepare("INSERT INTO tbl_due_payments 
                (booking_number, user_email, paid_amount, due_amount, total_amount, payment_method, status, paid_date) 
                VALUES 
                (:bookingNumber, :userEmail, :paidAmount, 0, :totalAmount, :paymentMethod, 'Paid', NOW())");
            $insertDue->execute([
                ':bookingNumber' => $bookingNumber,
                ':userEmail' => $booking->userEmail,
                ':paidAmount' => $payNow,
                ':totalAmount' => $totalAmount,
                ':paymentMethod' => $payment_method
            ]);
        }

        $success = "Payment successful! Your payment status is now <strong>$newStatus</strong>.";
        // Refresh
        $paidAmount = $newPaidAmount;
        $dueAmount = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Due Payment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5" style="max-width: 600px;">
  <h3>Due Payment for Booking: <?php echo htmlentities($bookingNumber); ?></h3>
  <div class="card p-3 mb-4">
    <p>Total Rent: <strong><?php echo number_format($totalAmount, 2); ?> tk</strong></p>
    <p>Paid: <strong><?php echo number_format($paidAmount, 2); ?> tk</strong></p>
    <p>Due: <strong><?php echo number_format($dueAmount, 2); ?> tk</strong></p>
  </div>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php elseif ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <a href="my-booking.php" class="btn btn-primary">Back to My Bookings</a>
  <?php elseif ($dueAmount > 0): ?>
    <form method="post">
      <div class="mb-3">
        <label>Enter Amount to Pay (Only <?php echo number_format($dueAmount, 2); ?> tk)</label>
        <input type="number" name="pay_now" class="form-control" step="0.01" max="<?php echo $dueAmount; ?>" required />
      </div>
      <div class="mb-3">
        <label>Payment Method</label>
        <select name="payment_method" class="form-select" required>
          <option value="">-- Select Method --</option>
          <option value="bKash">bKash</option>
          <option value="Nagad">Nagad</option>
          <option value="Card">Card</option>
          <option value="Cash">Cash</option>
        </select>
      </div>
      <button type="submit" class="btn btn-success w-100">Pay Now</button>
    </form>
  <?php endif; ?>
</div>
</body>
</html>