<?php
session_start();
include('includes/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'includes/phpmailer/PHPMailer.php';
require_once 'includes/phpmailer/SMTP.php';
require_once 'includes/phpmailer/Exception.php';

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit;
}

$bookingNumber = $_GET['booking'] ?? '';
if (!$bookingNumber) {
    echo "Invalid Booking."; exit;
}

// Fetch booking info
$sql = "SELECT 
          tblvehicles.VehiclesTitle,
          tblvehicles.Vimage1,
          tblbrands.BrandName,
          tblbooking.FromDate,
          tblbooking.ToDate,
          tblvehicles.PricePerDay,
          DATEDIFF(tblbooking.ToDate, tblbooking.FromDate) as totaldays,
          tblbooking.BookingNumber
        FROM tblbooking 
        JOIN tblvehicles ON tblbooking.VehicleId = tblvehicles.id 
        JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand 
        WHERE tblbooking.BookingNumber = :bookingNumber";
$query = $dbh->prepare($sql);
$query->bindParam(':bookingNumber', $bookingNumber);
$query->execute();
$booking = $query->fetch(PDO::FETCH_OBJ);

if (!$booking) {
    echo "Booking not found."; exit;
}

$tdays = $booking->totaldays + 1;
$totalPrice = $tdays * $booking->PricePerDay;
$minPayment = $totalPrice * 0.5;
$error = '';
$step = $_SESSION['step'] ?? 1;

// Step 1: Handle payment method and amount
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['payment_method']) && !isset($_POST['code_verify'])) {
    $payment_method = $_POST['payment_method'];
    $amount_to_pay = floatval($_POST['amount_to_pay'] ?? 0);
    $mobile = trim($_POST['mobile_number'] ?? '');
    $pin = trim($_POST['transaction_pin'] ?? '');
    $card = trim($_POST['card_number'] ?? '');
    $email = $_SESSION['login'];

    if ($amount_to_pay < $minPayment || $amount_to_pay > $totalPrice) {
        $error = "You must pay at least 50% of the total amount (" . number_format($minPayment, 2) . " tk).";
    } else {
        $due_amount = $totalPrice - $amount_to_pay;

        if ($payment_method != 'Cash') {
            if ($payment_method == 'Card' && empty($card)) {
                $error = "Please enter your card number.";
            } elseif (($payment_method == 'bKash' || $payment_method == 'Nagad') && (empty($mobile) || empty($pin))) {
                $error = "Please enter mobile number and PIN.";
            } else {
                $code = rand(100000, 999999);
                $_SESSION['payment_code'] = $code;
                $_SESSION['payment_data'] = [
                    'method' => $payment_method,
                    'mobile' => $mobile,
                    'pin' => $pin,
                    'card' => $card,
                    'amount' => $amount_to_pay,
                    'booking' => $bookingNumber,
                    'due' => $due_amount,
                    'total' => $totalPrice
                ];
                $_SESSION['step'] = 2;

                $dbh->prepare("UPDATE tblbooking SET verification_code = :code WHERE BookingNumber = :booking")
                    ->execute([':code' => $code, ':booking' => $bookingNumber]);

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'riponhossainmd744@gmail.com';
                    $mail->Password = 'fssekapdlfjldxwt';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('riponhossainmd744@gmail.com', 'Car Rental Portal');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Payment Verification Code';
                    $mail->Body = "Your verification code is: <strong>$code</strong>";
                    $mail->send();

                    $step = 2;
                } catch (Exception $e) {
                    $error = "Mailer Error: {$mail->ErrorInfo}";
                }
            }
        } else {
            // Cash payment
            $status = ($due_amount == 0) ? 'Paid' : 'Due';
            $dbh->prepare("UPDATE tblbooking SET payment_status=:status, payment_method='Cash', payment_amount=:paid WHERE BookingNumber=:booking")
                ->execute([
                    ':status' => $status,
                    ':paid' => $amount_to_pay,
                    ':booking' => $bookingNumber
                ]);

            if ($due_amount > 0) {
                $dbh->prepare("INSERT INTO tbl_due_payments (booking_number, user_email, paid_amount, due_amount, total_amount, payment_method, note)
                    VALUES (:booking, :email, :paid, :due, :total, 'Cash', 'Partial Cash Payment')")
                    ->execute([
                        ':booking' => $bookingNumber,
                        ':email' => $email,
                        ':paid' => $amount_to_pay,
                        ':due' => $due_amount,
                        ':total' => $totalPrice
                    ]);
            }

            echo "<script>alert('Payment saved successfully.'); location='my-booking.php';</script>"; exit;
        }
    }
}

// Step 2: Handle code verification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['code_verify'])) {
    if ($_POST['verification_code'] == $_SESSION['payment_code']) {
        $data = $_SESSION['payment_data'];
        $status = ($data['due'] == 0) ? 'Paid' : 'Due';

        $dbh->prepare("UPDATE tblbooking SET payment_status=:status, payment_method=:method, mobile_number=:mobile, transaction_pin=:pin, card_number=:card, payment_amount=:amount WHERE BookingNumber=:booking")
            ->execute([
                ':status' => $status,
                ':method' => $data['method'],
                ':mobile' => $data['mobile'],
                ':pin' => $data['pin'],
                ':card' => $data['card'],
                ':amount' => $data['amount'],
                ':booking' => $data['booking']
            ]);

        if ($data['due'] > 0) {
            $dbh->prepare("INSERT INTO tbl_due_payments (booking_number, user_email, paid_amount, due_amount, total_amount, payment_method, note)
                VALUES (:booking, :email, :paid, :due, :total, :method, 'Partial Payment Verified')")
                ->execute([
                    ':booking' => $data['booking'],
                    ':email' => $_SESSION['login'],
                    ':paid' => $data['amount'],
                    ':due' => $data['due'],
                    ':total' => $data['total'],
                    ':method' => $data['method']
                ]);
        }

        unset($_SESSION['payment_code'], $_SESSION['payment_data'], $_SESSION['step']);
        echo "<script>alert('Payment verified successfully.'); location='my-booking.php';</script>"; exit;
    } else {
        $error = "Invalid verification code.";
        $step = 2;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment for Booking <?php echo htmlentities($bookingNumber); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script>
    function toggleFields() {
      const method = document.getElementById('payment_method').value;
      const cardField = document.getElementById('card-field');
      const paymentField = document.getElementById('payment-details');
      const mobile = document.getElementById('mobile_number');
      const pin = document.getElementById('transaction_pin');
      const card = document.getElementById('card_number');

      if (method === 'Card') {
        cardField.style.display = 'block';
        paymentField.style.display = 'none';
        card.required = true;
        card.disabled = false;
        mobile.required = false;
        pin.required = false;
        mobile.disabled = true;
        pin.disabled = true;
      } else if (method === 'bKash' || method === 'Nagad') {
        cardField.style.display = 'none';
        paymentField.style.display = 'block';
        mobile.required = true;
        pin.required = true;
        mobile.disabled = false;
        pin.disabled = false;
        card.required = false;
        card.disabled = true;
      } else {
        cardField.style.display = 'none';
        paymentField.style.display = 'none';
        mobile.required = false;
        pin.required = false;
        card.required = false;
        mobile.disabled = true;
        pin.disabled = true;
        card.disabled = true;
      }
    }
    window.onload = toggleFields;
  </script>
</head>
<body>
<div class="container mt-5" style="max-width:600px;">
  <h3>Payment for Booking: <?php echo htmlentities($bookingNumber); ?></h3>
  <div class="card p-3 mb-4">
    <img src="admin/img/vehicleimages/<?php echo htmlentities($booking->Vimage1); ?>" width="200">
    <h5><?php echo htmlentities($booking->BrandName . " " . $booking->VehiclesTitle); ?></h5>
    <p><?php echo htmlentities($booking->FromDate . " to " . $booking->ToDate); ?></p>
    <p>Total Days: <?php echo htmlentities($tdays); ?> | Rate: <?php echo htmlentities($booking->PricePerDay); ?>tk</p>
    <p><strong>Total: <?php echo htmlentities($totalPrice); ?>tk</strong></p>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>

  <?php if ($step === 1): ?>
    <form method="post">
      <div class="mb-3">
        <label>Select Payment Method</label>
        <select name="payment_method" id="payment_method" class="form-select" onchange="toggleFields()" required>
          <option value="">-- Choose --</option>
          <option value="bKash">bKash</option>
          <option value="Nagad">Nagad</option>
          <option value="Card">Card</option>
          <option value="Cash">Cash</option>
        </select>
      </div>
      <div class="mb-3">
        <label>Total Amount</label>
        <input type="text" class="form-control" value="<?php echo number_format($totalPrice, 2); ?> tk" readonly>
      </div>

      <div class="mb-3">
        <label>Minimum Required (50%)</label>
        <input type="text" class="form-control" value="<?php echo number_format($minPayment, 2); ?> tk" readonly>
      </div>

      <div class="mb-3">
        <label>Enter Amount to Pay</label>
        <input type="number" name="amount_to_pay" class="form-control"
               min="<?php echo $minPayment; ?>"
               max="<?php echo $totalPrice; ?>"
               step="1"
               placeholder="Minimum: <?php echo number_format($minPayment, 2); ?> tk"
               required>
      </div>

      <div id="payment-details" style="display:none;">
        <div class="mb-3">
          <label>Mobile Number</label>
          <input type="text" id="mobile_number" name="mobile_number" class="form-control" maxlength="11" pattern="01[3-9][0-9]{8}">
        </div>
        <div class="mb-3">
          <label>Transaction PIN</label>
          <input type="password" id="transaction_pin" name="transaction_pin" class="form-control" maxlength="4">
        </div>
      </div>

      <div id="card-field" style="display:none;">
        <div class="mb-3">
          <label>Card Number</label>
          <input type="text" id="card_number" name="card_number" class="form-control" maxlength="16">
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Proceed to Verification</button>
    </form>
  <?php elseif ($step === 2): ?>
    <form method="post">
      <div class="mb-3">
        <label>Enter Verification Code</label>
        <input type="text" name="verification_code" class="form-control" maxlength="6" required>
      </div>
      <input type="hidden" name="code_verify" value="1">
      <button type="submit" class="btn btn-success w-100">Submit Code</button>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
