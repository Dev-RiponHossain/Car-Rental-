<?php
session_start();
/*error_reporting(0);*/
include('includes/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../includes/phpmailer/PHPMailer.php';
require_once '../includes/phpmailer/SMTP.php';
require_once '../includes/phpmailer/Exception.php';


if (isset($_REQUEST['aeid'])) {
    $aeid = intval($_GET['aeid']);
    $status = 1;

    // Update booking status
    $sql = "UPDATE tblbooking SET Status=:status WHERE id=:aeid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':aeid', $aeid, PDO::PARAM_STR);
    $query->execute();

    // Get user info
    $sql = "SELECT tblusers.FullName, tblusers.EmailId, tblbooking.BookingNumber 
            FROM tblbooking 
            JOIN tblusers ON tblbooking.userEmail = tblusers.EmailId 
            WHERE tblbooking.id = :aeid";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':aeid', $aeid, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if ($user) {
        // Fetch payment info from due payments
        $bookingNumber = $user->BookingNumber;
        $stmt2 = $dbh->prepare("SELECT total_amount, paid_amount, due_amount FROM tbl_due_payments WHERE booking_number = :bookingNumber");
        $stmt2->bindParam(':bookingNumber', $bookingNumber, PDO::PARAM_STR);
        $stmt2->execute();
        $paymentInfo = $stmt2->fetch(PDO::FETCH_OBJ);

        if ($paymentInfo) {
            $grandTotal = isset($paymentInfo->total_amount) ? floatval($paymentInfo->total_amount) : 0;
            $paidAmount = isset($paymentInfo->paid_amount) ? floatval($paymentInfo->paid_amount) : 0;
            $dueAmount  = isset($paymentInfo->due_amount) ? floatval($paymentInfo->due_amount) : ($grandTotal - $paidAmount);
        } else {
            // fallback if full payment and no row in due payments table
            $stmt3 = $dbh->prepare("SELECT payment_amount FROM tblbooking WHERE BookingNumber = :bookingNumber");
            $stmt3->bindParam(':bookingNumber', $bookingNumber, PDO::PARAM_STR);
            $stmt3->execute();
            $bookingPayment = $stmt3->fetch(PDO::FETCH_OBJ);

            $grandTotal = $bookingPayment ? floatval($bookingPayment->payment_amount) : 0;
            $paidAmount = $grandTotal;
            $dueAmount = 0;
        }

        // Send confirmation email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'riponhossainmd744@gmail.com';
            $mail->Password   = 'fssekapdlfjldxwt'; // Use App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('riponhossainmd744@gmail.com', 'Car Rental Portal');
            $mail->addAddress($user->EmailId, $user->FullName);
            $mail->isHTML(true);
            $mail->Subject = 'Booking Confirmation - Car Rental Portal';
            $mail->Body = '
                <div style="max-width:600px; margin:auto; padding:20px; font-family:Arial, sans-serif; border:1px solid #ddd; border-radius:8px; background-color:#f9f9f9;">
                    <div style="text-align:center; padding-bottom:20px;">
                        <h2 style="color:#007bff;">Car Rental Booking Confirmed!</h2>
                    </div>
                    
                    <p style="font-size:16px; color:#333;">Dear <strong>' . htmlspecialchars($user->FullName) . '</strong>,</p>
                    
                    <p style="font-size:15px; color:#333;">
                        We are pleased to inform you that your booking 
                        <span style="color:#28a745;"><strong>#' . htmlspecialchars($bookingNumber) . '</strong></span> 
                        has been <strong>successfully confirmed</strong>.
                    </p>
                    
                    <div style="background-color:#fff; padding:15px; margin:20px 0; border-left:4px solid #28a745; border-radius:4px;">
                        <p style="margin:0; font-size:15px;">
                            ✅ Booking Number: <strong>' . htmlspecialchars($bookingNumber) . '</strong><br>
                            🔒 Status: <strong style="color:green;">Confirmed</strong><br>' . 
                            ($dueAmount > 0 
                                ? '💸 Payment Status: <strong style="color:orange;">Partial Payment (Due: ' . $dueAmount . ' tk)</strong><br>
                                   💰 Paid Amount: <strong>' . $paidAmount . ' tk</strong><br>
                                   ⏳️ Due Amount: <strong style="color:red;">' . $dueAmount . ' tk</strong>'
                                : '💸 Payment Status: <strong style="color:green;">Paid (Full Payment)</strong><br>
                                   💰 Total Paid: <strong>' . $grandTotal . ' tk</strong>'
                            ) . '
                        </p>
                    </div>

                    <p style="font-size:15px; color:#555;">
                        You can log into your account anytime to view the full booking details.
                    </p>
                    
                    <div style="text-align:center; margin:30px 0;">
                        <a href="https://foxstar.xyz/index.php" 
                           style="padding:12px 25px; background-color:#007bff; color:#fff; text-decoration:none; border-radius:5px; font-weight:bold;">
                            🔑 Login to Your Account
                        </a>
                    </div>

                    <hr style="border:none; border-top:1px solid #ddd; margin:30px 0;">

                    <p style="font-size:14px; color:#666;">
                        Thank you for choosing <strong>Car Rental Portal</strong>. We look forward to serving you!
                    </p>

                    <p style="font-size:14px; color:#666;">
                        Warm regards,<br>
                        <strong style="color:#007bff;">Car Rental Team</strong>
                    </p>
                </div>
            ';

            $mail->send();
        } catch (Exception $e) {
            error_log('Mailer Error: ' . $mail->ErrorInfo);
        }
    }

    echo "<script>alert('Booking Successfully Confirmed');</script>";
    echo "<script type='text/javascript'> document.location = 'confirmed-bookings.php'; </script>";
    exit();
}


// Cancel Booking 
if (isset($_REQUEST['eid'])) {
    $eid = intval($_GET['eid']);
    $status = 2;

    $sql = "UPDATE tblbooking SET Status=:status WHERE id=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->execute();

    $sql = "SELECT tblusers.FullName, tblusers.EmailId, tblbooking.BookingNumber 
            FROM tblbooking 
            JOIN tblusers ON tblbooking.userEmail = tblusers.EmailId 
            WHERE tblbooking.id = :eid";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':eid', $eid, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if ($user) {
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
            $mail->addAddress($user->EmailId, $user->FullName);
            $mail->isHTML(true);
            $mail->Subject = 'Booking Cancelled - Car Rental Portal';
            $mail->Body = '
                           <div style="max-width:600px;margin:auto;padding:30px;border-radius:10px;border:1px solid #e0e0e0;background:#fff;font-family:Arial, sans-serif;">
                            <div style="text-align:center;padding-bottom:20px;">
                                <h2 style="color:#d9534f;margin-top:10px;">Booking Cancelled</h2>
                            </div>
                            <div style="font-size:16px;color:#333;">
                                <p>Dear <strong>' . htmlspecialchars($user->FullName) . '</strong>,</p>
                                <p>We regret to inform you that your booking <strong>#' . htmlspecialchars($user->BookingNumber) . '</strong> has been <span style="color:#d9534f;font-weight:bold;">cancelled</span>.</p>
                                <p>If you believe this was a mistake or have any questions, please feel free to contact our support team.</p>
                                <p style="margin-top:20px;"><strong>Refund Notice:</strong> If you have already made a payment for this booking, the amount will be refunded to your original payment method within 3–7 business days.</p>
                            </div>
                            <div style="margin-top:30px;text-align:center;">
                                <a href="https://foxstar.xyz" style="display:inline-block;padding:10px 20px;background:#d9534f;color:#fff;text-decoration:none;border-radius:5px;">Contact Support</a>
                            </div>
                            <div style="margin-top:30px;font-size:12px;color:#888;text-align:center;">
                                <p>&copy; ' . date("Y") . ' Car Rental Portal. All rights reserved.</p>
                            </div>
                        </div>';

            $mail->send();
        } catch (Exception $e) {
            // Optional: log the error
        }
    }

    echo "<script>alert('Booking Successfully Cancelled');</script>";
    echo "<script type='text/javascript'> document.location = 'canceled-bookings.php'; </script>";
    exit();
}

?>

<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#3e454c">
    
    <title>Car Rental Portal | Booking Details</title>

    <!-- Font awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Sandstone Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Bootstrap Datatables -->
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <!-- Bootstrap social button library -->
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <!-- Bootstrap select -->
    <link rel="stylesheet" href="css/bootstrap-select.css">
    <!-- Bootstrap file input -->
    <link rel="stylesheet" href="css/fileinput.min.css">
    <!-- Awesome Bootstrap checkbox -->
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <!-- Admin Stye -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap{
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #printArea, #printArea * {
                visibility: visible;
            }
            #printArea {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include('includes/header.php');?>

    <div class="ts-main-content">
        <?php include('includes/leftbar.php');?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title">Booking Details</h2>

                        <!-- Zero Configuration Table -->
                        <div class="panel panel-default">
                            <div class="panel-heading">Booking Info</div>
                            <div class="panel-body">
                                <div id="printArea">
                                    <table border="1" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                        <tbody>
                                            <?php 
                                            $bid = intval($_GET['bid']);
                                           $sql = "SELECT tblusers.*, tblbrands.BrandName, tblvehicles.VehiclesTitle, 
                                                   tblbooking.FromDate, tblbooking.ToDate, tblbooking.message, 
                                                   tblbooking.VehicleId as vid, tblbooking.Status, tblbooking.PostingDate, 
                                                   tblbooking.id, tblbooking.BookingNumber, tblbooking.payment_status, 
                                                   tblbooking.payment_amount, tblbooking.payment_method,
                                                   DATEDIFF(tblbooking.ToDate,tblbooking.FromDate) as totalnodays, 
                                                   tblvehicles.PricePerDay, tblbooking.address, tblbooking.district_name, 
                                                   tblbooking.division_name, tbl_due_payments.paid_amount, tbl_due_payments.due_amount, 
                                                   tbl_due_payments.total_amount, tbl_due_payments.payment_method as payment_method_detail,
                                                   tbl_due_payments.paid_date, tbl_due_payments.status as payment_status_detail, 
                                                   tbl_due_payments.note
                                                   FROM tblbooking
                                                   JOIN tblvehicles ON tblvehicles.id = tblbooking.VehicleId
                                                   JOIN tblusers ON tblusers.EmailId = tblbooking.userEmail
                                                   JOIN tblbrands ON tblvehicles.VehiclesBrand = tblbrands.id
                                                   LEFT JOIN tbl_due_payments ON tbl_due_payments.booking_number = tblbooking.BookingNumber
                                                   WHERE tblbooking.id = :bid";

                                            $query = $dbh->prepare($sql);
                                            $query->bindParam(':bid', $bid, PDO::PARAM_STR);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if ($query->rowCount() > 0) {
                                                foreach ($results as $result) {
                                                    $tdays = $result->totalnodays + 1;
                                                    $ppdays = $result->PricePerDay;
                                                    $grandTotal = $tdays * $ppdays;
                                                    $paidAmount = $result->paid_amount ?? 0;
                                                    $dueAmount = $result->due_amount ?? ($grandTotal - $paidAmount);
                                            ?>  
                                            <h3 style="text-align:center; color:red">#<?php echo htmlentities($result->BookingNumber); ?> Booking Details</h3>

                                            <tr>
                                                <th colspan="4" style="text-align:center;color:blue">User Details</th>
                                            </tr>
                                            <tr>
                                                <th>Booking No.</th>
                                                <td>#<?php echo htmlentities($result->BookingNumber); ?></td>
                                                <th>Name</th>
                                                <td><?php echo htmlentities($result->FullName); ?></td>
                                            </tr>
                                            <tr>                                            
                                                <th>Email Id</th>
                                                <td><?php echo htmlentities($result->EmailId); ?></td>
                                                <th>Contact No</th>
                                                <td><?php echo htmlentities($result->ContactNo); ?></td>
                                            </tr>
                                            <tr>                                            
                                                <th>Address</th>
                                                <td><?php echo htmlentities($result->Address); ?></td>
                                                <th>City</th>
                                                <td><?php echo htmlentities($result->City); ?></td>
                                            </tr>
                                            <tr>                                            
                                                <th>Country</th>
                                                <td colspan="3"><?php echo htmlentities($result->Country); ?></td>
                                            </tr>

                                            <tr>
                                                <th colspan="4" style="text-align:center;color:blue">Booking Details</th>
                                            </tr>
                                            <tr>                                            
                                                <th>Vehicle Name</th>
                                                <td><a href="#"><?php echo htmlentities($result->BrandName); ?>, <?php echo htmlentities($result->VehiclesTitle); ?></a></td>
                                                <th>Booking Date</th>
                                                <td><?php echo htmlentities($result->PostingDate); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Destination</th>
                                                <td colspan="3">
                                                    <?php echo htmlentities($result->address); ?>,
                                                    <?php echo htmlentities($result->district_name); ?>,
                                                    <?php echo htmlentities($result->division_name); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>From Date</th>
                                                <td><?php echo htmlentities($result->FromDate); ?></td>
                                                <th>To Date</th>
                                                <td><?php echo htmlentities($result->ToDate); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Total Days</th>
                                                <td><?php echo htmlentities($tdays); ?></td>
                                                <th>Rent Per Day</th>
                                                <td><?php echo htmlentities($ppdays); ?> tk</td>
                                            </tr>
                                            <tr>
                                                <th colspan="3" style="text-align:center">Grand Total</th>
                                                <td><?php echo htmlentities($grandTotal); ?> tk</td>
                                            </tr>

                                            <tr>
                                                <th colspan="4" style="text-align:center;color:blue">Payment Details</th>
                                            </tr>
                                            <tr>
                                                <th>Payment Status</th>
                                                <td>
                                                    <?php 
                                                    if ($result->payment_status == 'Pending') {
                                                        echo '<span style="color:orange;font-weight:bold;">Pending</span>';
                                                    } elseif ($result->payment_status == 'Due') {
                                                        echo '<span style="color:red;font-weight:bold;">Due ('.htmlentities($dueAmount).' tk)</span>';
                                                    } else {
                                                        echo '<span style="color:green;font-weight:bold;">Paid</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <th>Payment Method</th>
                                                <td><?php echo htmlentities($result->payment_method ?? $result->payment_method_detail ?? 'N/A'); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Paid Amount</th>
                                                <td><?php echo htmlentities($paidAmount); ?> tk</td>
                                                <th>Due Amount</th>
                                                <td><?php echo htmlentities($dueAmount); ?> tk</td>
                                            </tr>
                                            <?php if (!empty($result->paid_date)) : ?>
                                            <tr>
                                                <th>Payment Date</th>
                                                <td colspan="3"><?php echo htmlentities($result->paid_date); ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($result->note)) : ?>
                                            <tr>
                                                <th>Payment Note</th>
                                                <td colspan="3"><?php echo htmlentities($result->note); ?></td>
                                            </tr>
                                            <?php endif; ?>

                                            <tr>
                                                <th>Booking Status</th>
                                                <td>
                                                    <?php 
                                                    if($result->Status == 0) {
                                                        echo htmlentities('Not Confirmed yet');
                                                    } else if ($result->Status == 1) {
                                                        echo htmlentities('Confirmed');
                                                    } else {
                                                        echo htmlentities('Cancelled');
                                                    }
                                                    ?>
                                                </td>
                                                <th>Last Updation Date</th>
                                                <td><?php echo htmlentities($result->LastUpdationDate ?? 'N/A'); ?></td>
                                            </tr>

                                            <?php if($result->Status == 0) { ?>
                                            <tr>    
                                                <td style="text-align:center" colspan="4">
                                                    <a href="bookig-details.php?aeid=<?php echo htmlentities($result->id); ?>" onclick="return confirm('Do you really want to Confirm this booking')" class="btn btn-primary"> Confirm Booking</a> 
                                                    <a href="bookig-details.php?eid=<?php echo htmlentities($result->id); ?>" onclick="return confirm('Do you really want to Cancel this Booking')" class="btn btn-danger"> Cancel Booking</a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <?php 
                                                $cnt = $cnt + 1; 
                                                } 
                                            } 
                                            ?>
                                        </tbody>
                                    </table>
                                    <form method="post">
                                        <input name="Submit2" type="submit" class="txtbox4" value="Print" onClick="return f3();" style="cursor: pointer;" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/fileinput.js"></script>
    <script src="js/chartData.js"></script>
    <script src="js/main.js"></script>
    <script language="javascript" type="text/javascript">
        function f3() {
            window.print(); 
        }
    </script>
</body>
</html>