<?php
session_start();
include('include/config.php');
include('include/checklogin.php');
check_login();

// Include your USER class
require_once 'class.user.php';
$user = new USER();

if(isset($_POST['submit'])) {
    $specilization = $_POST['Doctorspecialization'];
    $doctorid = $_POST['doctor'];
    $userid = $_SESSION['id'];
    $fees = $_POST['fees'];
    $appdate = $_POST['appdate'];
    $userstatus = 1;
    $docstatus = 1;

    // Get user's wallet balance
    $user_query = mysqli_query($con, "SELECT wallet_balance FROM users WHERE id = '$userid'");
    $user_data = mysqli_fetch_assoc($user_query);
    $wallet_balance = $user_data['wallet_balance'];

    // Check if user has sufficient balance
    if($wallet_balance < $fees) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Insufficient Balance',
            'message' => 'You don\'t have enough balance in your wallet. Please recharge your wallet.'
        ];
        header("Location: book-appointment.php");
        exit();
    }

    // STRONGER VALIDATION - Check for existing appointment FIRST
    $existing_query = mysqli_query($con, "SELECT id FROM appointment WHERE 
                                        doctorId = '$doctorid' AND 
                                        userId = '$userid' AND 
                                        appointmentDate = '$appdate' AND
                                        (userStatus = 1 OR doctorStatus = 1)");
    
    if(mysqli_num_rows($existing_query) > 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Booking Failed',
            'message' => 'You already have an active appointment with this doctor on '.date('F j, Y', strtotime($appdate)).'.'
        ];
        header("Location: book-appointment.php");
        exit();
    }
    
    // Get the next serial number for this doctor on this date
    $serial_query = mysqli_query($con, "SELECT MAX(serialNumber) as max_serial FROM appointment WHERE doctorId = '$doctorid' AND appointmentDate = '$appdate'");
    $serial_data = mysqli_fetch_assoc($serial_query);
    $serialNumber = ($serial_data['max_serial'] ?? 0) + 1;
    
    // Start transaction
    mysqli_begin_transaction($con);
    
    try {
        // Deduct fee from user's wallet
        $update_wallet = mysqli_query($con, "UPDATE users SET wallet_balance = wallet_balance - $fees WHERE id = '$userid'");
        
        if(!$update_wallet) {
            throw new Exception("Failed to deduct from wallet");
        }
        
        // Insert appointment with serial number and payment details
        $query = mysqli_query($con, "INSERT INTO appointment(
            doctorSpecialization, 
            doctorId, 
            userId, 
            consultancyFees, 
            appointmentDate, 
            serialNumber, 
            userStatus, 
            doctorStatus,
            payment_status,
            payment_amount,
            payment_method
        ) VALUES(
            '$specilization',
            '$doctorid',
            '$userid',
            '$fees',
            '$appdate',
            '$serialNumber',
            '$userstatus',
            '$docstatus',
            'Paid',
            '$fees',
            'Wallet'
        )");

        // Include PDF generation function
        require_once 'generate_pdf.php';

        // Get the new appointment ID
        $appointmentId = mysqli_insert_id($con);
        
        // Generate PDF
        $pdfPath = generateAppointmentPDF($appointmentId, $con);
        
        
        // Commit transaction
        mysqli_commit($con);
        
        // Get user details for email
        $user_query = mysqli_query($con, "SELECT * FROM users WHERE id = '$userid'");
        $user_data = mysqli_fetch_assoc($user_query);
        
        // Get doctor details
        $doctor_query = mysqli_query($con, "SELECT * FROM doctors WHERE id = '$doctorid'");
        $doctor = mysqli_fetch_assoc($doctor_query);
        
        // Prepare email content
        $email = $user_data['email'];
        $subject = "Appointment Confirmation";
        
        $message = "
            <h2>Appointment Confirmation</h2>
            <p>Dear ".$user_data['fullName'].",</p>
            <p>Your appointment has been successfully booked with the following details:</p>
            <table border='1' cellpadding='5' cellspacing='0'>
                <tr><th>Doctor</th><td>".$doctor['doctorName']."</td></tr>
                <tr><th>Specialization</th><td>".$specilization."</td></tr>
                <tr><th>Date</th><td>".$appdate."</td></tr>
                <tr><th>Visiting Hours</th><td>".date('h:i A', strtotime($doctor['visiting_start_time']))." to ".date('h:i A', strtotime($doctor['visiting_end_time']))."</td></tr>
                <tr><th>Serial Number</th><td>".$serialNumber."</td></tr>
                <tr><th>Fees</th><td>".$fees." BDT</td></tr>
                <tr><th>Payment Status</th><td>Paid (via Wallet)</td></tr>
            </table>
            <p>Please find attached your appointment slip.</p>
            <p>Please arrive at the hospital according to your serial number.</p>
            <p>Thank you for choosing our service.</p>
        ";
        
        // Send email using USER class
        if($user->sendMail($email, $message, $subject, $pdfPath)) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'title' => 'Appointment Booked!',
                'message' => 'Your appointment has been successfully booked. Your serial number is: '.$serialNumber.'. A confirmation email with appointment slip has been sent.'
            ];
        } else {
            $_SESSION['alert'] = [
                'type' => 'success',
                'title' => 'Appointment Booked!',
                'message' => 'Your appointment has been successfully booked. Your serial number is: '.$serialNumber.'. <a href="download_slip.php?id='.$appointmentId.'">Download Appointment Slip</a>'
            ];
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($con);
        
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Booking Failed',
            'message' => 'Failed to book appointment: '.$e->getMessage()
        ];
    }
    
    header("Location: book-appointment.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User | Book Appointment</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ancizar+Serif:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="logo_no_bg.png?v=1.1" type="image/jpeg">

    <style>
        :root {
            --primary: #0866ff;
            --primary-dark: #1877f2;
            --dark: #212529;
            --light: #f8f9fa;
            --danger: #dc3545;
            --muted: #6c757d;
            --border-color: #dee2e6;
            --bg-card: #ffffff;
            --shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            --radius: 12px;
        }

        body {
            font-family: 'Ancizar Serif', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            font-size: 16px;
        }

        .container {
            padding: 50px 0;
        }

        .mainTitle {
            font-weight: 700;
            color: var(--dark);
            font-size: 2.25rem;
            margin-bottom: 30px;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 20px;
        }

        .appointment-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 35px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .appointment-card h5 {
            font-weight: 600;
            margin-bottom: 25px;
            font-size: 1.25rem;
        }

        form label {
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--dark);
            font-size: 20px;
        }

        .form-control, .form-select {
            border-radius: var(--radius);
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.15rem rgba(32, 201, 151, 0.25);
        }

        .form-select:disabled, .form-control:disabled {
            background-color: #e9ecef;
        }

        .btn-submit {
            background: var(--primary);
            color: #fff;
            padding: 14px 32px;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(8, 102, 255, 0.3);
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(24, 119, 242, 0.4);
        }

        .time-note {
            font-size: 0.85rem;
            color: var(--muted);
            margin-top: 5px;
        }

        .alert {
            border-radius: var(--radius);
            padding: 12px 16px;
            font-weight: 500;
        }

        .is-invalid {
            border-color: var(--danger) !important;
            box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.25) !important;
        }

        .logout-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, #ff7675, #d63031);
            color: #fff;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-size: 1rem;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            z-index: 1100;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #d63031, #ff7675);
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
        }

        @media (max-width: 768px) {
            .appointment-card {
                padding: 25px;
            }

            .mainTitle {
                font-size: 1.75rem;
            }
        }
        .selectable-date {
            cursor: pointer !important;
        }
        .text-muted {
            color: #adb5bd !important;
        }
        /* Professional Datepicker Styling */
        .professional-datepicker {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 15px;
            background: white;
        }

        .datepicker table {
            width: 100%;
        }

        .datepicker table tr td, 
        .datepicker table tr th {
            text-align: center;
            padding: 8px 12px;
            border-radius: 4px;
        }

        .datepicker table tr td.day:hover {
            background: #f0f0f0;
        }

        .datepicker table tr td.available-date {
            color: var(--primary);
            font-weight: 500;
        }

        .datepicker table tr td.available-date:hover {
            background: var(--primary);
            color: white;
        }

        .datepicker table tr td.active {
            background: var(--primary) !important;
            color: white !important;
        }

        .datepicker table tr td.today-date {
            color: #6c757d;
            font-weight: normal;
        }

        .datepicker table tr td.date-disabled,
        .datepicker table tr td.sunday-date {
            color: #adb5bd !important;
            text-decoration: line-through;
        }

        .datepicker .datepicker-switch {
            font-weight: 600;
            color: var(--dark);
        }

        .datepicker .prev, 
        .datepicker .next {
            color: var(--primary);
        }

        .datepicker .dow {
            font-weight: 600;
            color: var(--dark);
        }

        .datepicker .datepicker-days {
            padding: 5px;
        }

        /* Visiting hours display */
        #visiting-hours {
            font-size: 0.9rem;
            margin-top: 5px;
            padding: 8px;
            border-radius: 4px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        #visiting-hours i {
            margin-right: 5px;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }
        
        .wallet-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        
        .wallet-balance {
            font-weight: 700;
            color: #28a745;
            font-size: 1.1rem;
        }
        .available-date {
            background-color: #20c997 !important;
            color: white !important;
            border-radius: 50%;
            transition: 0.3s;
            cursor: pointer;
            font-size: 21px !important; 
            font-weight: bold; 
        }

        .available-date:hover {
            background-color: #17a2b8 !important;
        }

    </style>
</head>
<body>
    <div id="app py-6">
        <?php //include_once('include/header_patient_page.php'); ?>
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="row row w-100 justify-content-center">
                <!-- Left Side: Page Title -->
                <div class="col-md-6 d-flex justify-content-center align-items-center">
                    <h1 style="color: #00b894;" class="mainTitle">Book Appointment</h1>
                </div>

                <!-- Right Side: Appointment Form Card -->
                <div class="col-md-6">
                    <div class="appointment-card py-5">
                        <div class="card-body">
                            <?php if(isset($_SESSION['msg1'])): ?>
                                <div class="alert alert-danger"><?php echo htmlentities($_SESSION['msg1']); ?></div>
                                <?php unset($_SESSION['msg1']); ?>
                            <?php endif; ?>

                            <form role="form" name="book" method="post">

                                <!-- Wallet Balance Display -->
                                <div class="wallet-section mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i style="color: #00b894;" class="fas fa-wallet me-2"></i>
                                            <span>Wallet Balance:</span>
                                        </div>
                                        <div class="wallet-balance">
                                            <?php 
                                                $stmt = $con->prepare("SELECT wallet_balance FROM users WHERE id=?");
                                                $stmt->bind_param("i", $_SESSION['id']);
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                $user_data = $result->fetch_assoc();
                                                $balance = $user_data['wallet_balance'] ?? 0;
                                                echo number_format($balance, 2).' BDT';
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Doctor Specialization -->
                                <div class="mb-3 row align-items-center">
                                    <label for="Doctorspecialization" class="col-sm-5">
                                        <i style="color: #00b894;" class="fas fa-stethoscope me-2"></i> Specialization
                                    </label>
                                    <div class="col-sm-7">
                                        <select name="Doctorspecialization" class="form-select" id="Doctorspecialization" onChange="getdoctor(this.value);" required>
                                            <option value="" selected disabled>Select Specialization</option>
                                            <?php 
                                                $ret = mysqli_query($con, "select * from doctorspecilization");
                                                while($row = mysqli_fetch_array($ret)) {
                                                    echo '<option value="'.htmlentities($row['specilization']).'">'.htmlentities($row['specilization']).'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Doctors -->
                                <div class="mb-3 row align-items-center">
                                    <label for="doctor" class="col-sm-5">
                                        <i style="color: #00b894;" class="fas fa-user-md me-2"></i> Doctor
                                    </label>
                                    <div class="col-sm-7">
                                        <select name="doctor" class="form-select" id="doctor" onChange="getfee(this.value);" required>
                                            <option value="">Select Doctor</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Consultancy Fees -->
                                <div class="mb-3 row align-items-center">
                                    <label for="fees" class="col-sm-5">
                                        <i style="color: #00b894;" class="fas fa-money-bill-wave me-2"></i> Fees
                                    </label>
                                    <div class="col-sm-7">
                                        <select name="fees" class="form-select" id="fees" readonly>
                                            <option value="">Select Doctor First</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Appointment Date -->
                                <div class="mb-3 row align-items-center">
                                    <label for="appdate" class="col-sm-5">
                                        <i style="color: #00b894;" class="far fa-calendar-alt me-2"></i> Date
                                    </label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control datepicker" name="appdate" id="appdate" required data-date-format="yyyy-mm-dd">
                                    </div>
                                </div>

                                <!-- Visiting Hours Display -->
                                <div class="mb-3 row">
                                    <div class="col-sm-7 offset-sm-5">
                                        <div id="visiting-hours" class="small text-muted mt-2 p-2 bg-light rounded">
                                            <i class="fas fa-clock me-2"></i>
                                            <span>Select a doctor to view visiting hours</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="mt-5 ">
                                    <button type="submit" name="submit" class="btn btn-submit btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i> Book Appointment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Logout Button -->
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>

        <?php //include('include/footer.php'); ?>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    function getdoctor(val) {
        $.ajax({
            type: "POST",
            url: "get_doctor.php",
            data: {specilizationid: val},
            success: function(data) {
                $("#doctor").html(data);
                $("#fees").html('<option value="">Select Doctor First</option>');
                $("#visiting-hours").html('<i class="fas fa-clock me-2"></i><span>Select a doctor to view visiting hours</span>');
            },
            error: function() {
                alert("Error fetching doctors");
            }
        });
    }

    function getfee(val) {
        if(!val) return;
        
        $.ajax({
            type: "POST",
            url: "get_doctor.php",
            data: {doctor: val},
            success: function(data) {
                $("#fees").html(data);
                getVisitingHours(val);
            },
            error: function() {
                alert("Error fetching fees");
            }
        });
    }

    function getVisitingHours(doctorId) {
        if (!doctorId) {
            $('#visiting-hours').html('<i class="fas fa-clock me-2"></i><span>Select a doctor to view visiting hours</span>');
            return;
        }

        $.ajax({
            type: "POST",
            url: "get_visiting_hours.php",
            data: { doctorId: doctorId },
            dataType: "json",
            success: function(data) {
                if (data.start_time && data.end_time) {
                    let startTime = formatTime(data.start_time);
                    let endTime = formatTime(data.end_time);
                    $('#visiting-hours').html(
                        `<i class="fas fa-clock me-2"></i><span>Visiting hours: <strong>${startTime} to ${endTime}</strong></span>`
                    ).removeClass('text-danger').addClass('text-success');
                } else {
                    $('#visiting-hours').html(
                        '<i class="fas fa-exclamation-circle me-2"></i><span>No visiting hours set for this doctor</span>'
                    ).removeClass('text-success').addClass('text-danger');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                $('#visiting-hours').html(
                    '<i class="fas fa-exclamation-circle me-2"></i><span>Error loading visiting hours</span>'
                ).removeClass('text-success').addClass('text-danger');
            }
        });
    }

    function formatTime(timeString) {
        if(!timeString) return "N/A";
        
        let time = timeString.split(':');
        let hours = parseInt(time[0]);
        let minutes = time[1] || '00';
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        return hours + ':' + minutes + ' ' + ampm;
    }
    
    $(document).ready(function() {
        // Initialize datepicker with proper options
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            startDate: '+1d',
            endDate: '+2d',
            autoclose: true,
            todayHighlight: true,
            daysOfWeekDisabled: [5], // Disable Sundays
            weekStart: 0, // Start week on Monday
            templates: {
                leftArrow: '<i class="fas fa-chevron-left"></i>',
                rightArrow: '<i class="fas fa-chevron-right"></i>'
            },
            beforeShowDay: function(date) {
                var day = date.getDay();
                var today = new Date();
                today.setHours(0,0,0,0);
                
                // Disable past dates and dates beyond 30 days
                if (date < today || date > new Date(today.getTime() + (2 * 24 * 60 * 60 * 1000))) {
                    return {
                        enabled: false,
                        classes: 'date-disabled text-muted',
                        tooltip: 'Not available'
                    };
                }
                
                // Today's date styling
                if (date.getTime() === today.getTime()) {
                    return {
                        enabled: false,
                        classes: 'date-disabled today-date',
                        tooltip: 'Please select a future date'
                    };
                }
                
                // Available dates
                return {
                    enabled: true,
                    classes: 'available-date',
                    tooltip: 'Available for booking'
                };
            }
        }).on('show', function() {
            // Add professional styling to the datepicker container
            $('.datepicker').addClass('professional-datepicker');
        });

        // When doctor selection changes
        $('#doctor').change(function() {
            getfee($(this).val());
        });

        // Form submission handling
        $('form[name="book"]').submit(function(e) {
            let isValid = true;
            $('.is-invalid').removeClass('is-invalid');

            // Validate required fields
            $(this).find('[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please fill all required fields',
                    confirmButtonColor: '#20c997'
                });
                return false;
            }
            
            // Check wallet balance
            const fee = parseFloat($('#fees').val());
            const balanceText = $('.wallet-balance').text().trim();
            const balance = parseFloat(balanceText.replace(/[^\d.]/g, ''));
            
            if (fee > balance) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Insufficient Balance',
                    html: `You don't have enough balance in your wallet.<br>Required: ${fee.toFixed(2)} BDT<br>Available: ${balance.toFixed(2)} BDT`,
                    confirmButtonColor: '#20c997'
                });
                return false;
            }

            return true;
        });

        <?php if(isset($_SESSION['alert'])): ?>
        Swal.fire({
            icon: '<?php echo $_SESSION['alert']['type']; ?>',
            title: '<?php echo $_SESSION['alert']['title']; ?>',
            text: '<?php echo $_SESSION['alert']['message']; ?>',
            confirmButtonColor: '#20c997'
        });
        <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
    });
    </script>
</body>
</html>