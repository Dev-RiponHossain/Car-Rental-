<?php
session_start();
include('includes/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../includes/phpmailer/PHPMailer.php';
require_once '../includes/phpmailer/SMTP.php';
require_once '../includes/phpmailer/Exception.php';

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

$date = date('Y-m-d');

// Handle due payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_due'])) {
    $booking_number = $_POST['booking_number'];
    $payment_method = $_POST['payment_method'];
    $comments = $_POST['comments'] ?? '';
    $payment_date = date('Y-m-d');

    if ($payment_method !== 'Cash') {
        $_SESSION['error'] = "Only Cash payment method is allowed.";
        header("Location: due-payments.php");
        exit;
    }

    // Fetch due row
    $stmt = $dbh->prepare("SELECT id, due_amount, total_amount FROM tbl_due_payments WHERE booking_number = :booking_number ORDER BY id DESC LIMIT 1");
    $stmt->bindParam(':booking_number', $booking_number);
    $stmt->execute();
    $lastPayment = $stmt->fetch(PDO::FETCH_OBJ);

    if ($lastPayment && $lastPayment->due_amount > 0) {
        $pay_amount = $lastPayment->due_amount;

        // Update payment table
        $update = $dbh->prepare("UPDATE tbl_due_payments SET 
            paid_amount = paid_amount + :pay_amount, 
            due_amount = 0,
            status = 'Paid',
            note = :note,
            payment_method = :payment_method,
            paid_date = :payment_date
        WHERE id = :id");
        $update->execute([
            ':pay_amount' => $pay_amount,
            ':note' => $comments,
            ':payment_method' => $payment_method,
            ':payment_date' => $payment_date,
            ':id' => $lastPayment->id
        ]);

        // Update booking table
        $updateBooking = $dbh->prepare("UPDATE tblbooking SET payment_status = 'Paid' WHERE BookingNumber = :booking_number");
        $updateBooking->bindParam(':booking_number', $booking_number);
        $updateBooking->execute();

        // Insert log
        $logInsert = $dbh->prepare("INSERT INTO tbl_due_payment_logs (booking_number, due_paid_amount, payment_date) VALUES (:booking_number, :due_paid_amount, :payment_date)");
        $logInsert->execute([
            ':booking_number' => $booking_number,
            ':due_paid_amount' => $pay_amount,
            ':payment_date' => $payment_date
        ]);

        // ✅ Get user email
        $userStmt = $dbh->prepare("SELECT u.FullName, u.EmailId FROM tblusers u JOIN tblbooking b ON u.EmailId = b.userEmail WHERE b.BookingNumber = :booking_number");
        $userStmt->bindParam(':booking_number', $booking_number);
        $userStmt->execute();
        $user = $userStmt->fetch(PDO::FETCH_OBJ);

        if ($user) {
            // ✅ Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'riponhossainmd744@gmail.com';
                $mail->Password   = 'fssekapdlfjldxwt'; // Replace with App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('riponhossainmd744@gmail.com', 'Car Rental Portal');
                $mail->addAddress($user->EmailId, $user->FullName);

                $mail->isHTML(true);
                $mail->Subject = 'Due Payment Received - Car Rental Portal';
$mail->Body = '
    <div style="max-width:600px; margin:auto; padding:20px; font-family:Arial, sans-serif; border:1px solid #ddd; border-radius:8px; background-color:#f9f9f9;">
        <div style="text-align:center; padding-bottom:20px;">
            <h2 style="color:#28a745;">✅ Due Payment Received!</h2>
        </div>

        <p style="font-size:16px; color:#333;">Dear <strong>' . htmlspecialchars($user->FullName) . '</strong>,</p>

        <p style="font-size:15px; color:#333;">
            We have successfully received the due payment for your booking 
            <span style="color:#28a745;"><strong>#' . htmlspecialchars($booking_number) . '</strong></span>.
        </p>

        <div style="background-color:#fff; padding:15px; margin:20px 0; border-left:4px solid #28a745; border-radius:4px;">
            <p style="margin:0; font-size:15px;">
                ✅ Booking Number: <strong>' . htmlspecialchars($booking_number) . '</strong><br>
                🔒 Payment Status: <strong style="color:green;">Paid in Full</strong><br>
                💰 Paid Amount: <strong>' . number_format($pay_amount, 2) . ' tk</strong><br>
                ❌ Remaining Due: <strong style="color:red;">0 tk</strong><br>
                📅 Payment Date: <strong>' . date('F j, Y') . '</strong>
            </p>
        </div>

        <p style="font-size:15px; color:#555;">
            You can log into your account anytime to view your full booking and payment history.
        </p>

        <div style="text-align:center; margin:30px 0;">
            <a href="https://foxstar.xyz/index.php" 
               style="padding:12px 25px; background-color:#007bff; color:#fff; text-decoration:none; border-radius:5px; font-weight:bold;">
                🔑 Login to Your Account
            </a>
        </div>

        <hr style="border:none; border-top:1px solid #ddd; margin:30px 0;">

        <p style="font-size:14px; color:#666;">
            Thank you for completing your payment with <strong>Car Rental Portal</strong>. We look forward to serving you again!
        </p>

        <p style="font-size:14px; color:#666;">
            Warm regards,<br>
            <strong style="color:#007bff;">Car Rental Team</strong>
        </p>
    </div>
';

                $mail->send();
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
            }
        }

        $_SESSION['msg'] = "Due payment of " . number_format($pay_amount, 2) . " tk received successfully.";
    } else {
        $_SESSION['error'] = "No due amount found for this booking.";
    }

    header("Location: due-payments.php");
    exit;
}

// 📌 Rest of the HTML/JS part remains same (table, modal, jQuery)


// Fetch due bookings
$sql = "SELECT 
    b.BookingNumber,
    u.FullName,
    latest_dp.total_amount,
    IFNULL(SUM(dp_all.paid_amount), 0) AS total_paid,
    latest_dp.due_amount
FROM tblbooking b
JOIN tblusers u ON u.EmailId = b.userEmail
LEFT JOIN tbl_due_payments dp_all ON dp_all.booking_number = b.BookingNumber
LEFT JOIN (
    SELECT d1.booking_number, d1.due_amount, d1.total_amount
    FROM tbl_due_payments d1
    INNER JOIN (
        SELECT booking_number, MAX(id) AS max_id
        FROM tbl_due_payments
        GROUP BY booking_number
    ) d2 ON d1.booking_number = d2.booking_number AND d1.id = d2.max_id
) AS latest_dp ON latest_dp.booking_number = b.BookingNumber
WHERE b.Status = 1
GROUP BY b.BookingNumber, u.FullName, latest_dp.due_amount, latest_dp.total_amount
HAVING latest_dp.due_amount > 0";

$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <title>Due Payments Report</title>
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

            <h2 class="page-title text-center mb-4">Today's Due Payment Bookings (<?= htmlentities($date) ?>)</h2>

            <?php if (isset($_SESSION['msg'])): ?>
                <div class="alert alert-success text-center"><?= htmlentities($_SESSION['msg']) ?></div>
                <?php unset($_SESSION['msg']); endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger text-center"><?= htmlentities($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); endif; ?>

            <div class="panel panel-default">
                <div class="panel-heading"><h4 style="font-weight: bold;">Bookings with Due Payment</h4></div>
                <div class="panel-body">
                <?php if (count($results) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Booking No</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $cnt = 1;
                            foreach ($results as $row):
                            ?>
                            <tr>
                                <td><?= $cnt++ ?></td>
                                <td><?= htmlentities($row->BookingNumber) ?></td>
                                <td><?= htmlentities($row->FullName) ?></td>
                                <td><?= number_format($row->total_amount, 2) ?> tk</td>
                                <td><?= number_format($row->total_paid, 2) ?> tk</td>
                                <td><strong style="color:red"><?= number_format($row->due_amount, 2) ?> tk</strong></td>
                                <td>
                                    <!-- Button trigger modal -->
                                    <button type="button"
                                        class="btn btn-primary btn-sm payNowBtn"
                                        data-booking="<?= $row->BookingNumber ?>"
                                        data-due="<?= number_format($row->due_amount, 2) ?>">
                                        Pay <?= number_format($row->due_amount, 2) ?> tk
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">No due payments found.</div>
                <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Payment Confirmation Modal -->
<div class="modal fade" id="confirmPaymentModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg">
      <form method="post" id="confirmPaymentForm">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title font-weight-bold" id="confirmModalLabel">Confirm Due Payment</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4">
          <div class="text-center mb-4">
            <i class="fas fa-money-bill-wave fa-3x text-primary mb-3"></i>
            <h4 class="font-weight-bold mb-2">Payment Confirmation</h4>
            <p class="text-muted">You are about to process a due payment</p>
          </div>
          
          <div class="alert alert-light border rounded p-3 mb-4">
            <div class="d-flex justify-content-between mb-2">
              <span class="font-weight-bold">Booking Number:</span>
              <span class="text-primary" id="modalBookingNumberDisplay"></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="font-weight-bold">Payment Method:</span>
              <span>Cash</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="font-weight-bold">Amount:</span>
              <span class="text-success font-weight-bold" id="modalDueAmount">0 tk</span>
            </div>
          </div>
          
          <input type="hidden" name="booking_number" id="modalBookingNumber">
          <input type="hidden" name="payment_method" value="Cash">
          <input type="hidden" name="pay_due" value="1">
        </div>
        <div class="modal-footer bg-light d-flex justify-content-between">
          <button type="button" class="btn btn-outline-secondary font-weight-bold" data-dismiss="modal">
            <i class="fas fa-times mr-2"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary font-weight-bold">
            <i class="fas fa-check-circle mr-2"></i> Confirm Payment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
$(document).ready(function () {
    $('.payNowBtn').on('click', function () {
        var bookingNumber = $(this).data('booking');
        var dueAmount = $(this).data('due');

        $('#modalBookingNumber').val(bookingNumber);
        $('#modalBookingNumberDisplay').text(bookingNumber);
        $('#modalDueAmount').text(dueAmount + ' tk');

        $('#confirmPaymentModal').modal('show');
    });
});
</script>
</body>
</html>
