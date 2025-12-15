<?php
// Include PHPMailer classes at the top
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'includes/phpmailer/PHPMailer.php';
require_once 'includes/phpmailer/SMTP.php';
require_once 'includes/phpmailer/Exception.php';

// error_reporting(0);
if(isset($_POST['signup']))
{
    $fname=$_POST['fullname'];
    $email=$_POST['emailid']; 
    $mobile=$_POST['mobileno'];
    $password=md5($_POST['password']); 

    $sql="INSERT INTO tblusers(FullName,EmailId,ContactNo,Password) VALUES(:fname,:email,:mobile,:password)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':fname',$fname,PDO::PARAM_STR);
    $query->bindParam(':email',$email,PDO::PARAM_STR);
    $query->bindParam(':mobile',$mobile,PDO::PARAM_STR);
    $query->bindParam(':password',$password,PDO::PARAM_STR);
    $query->execute();

    $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId)
    {
        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'riponhossainmd744@gmail.com';
            $mail->Password   = 'fssekapdlfjldxwt'; // App password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('riponhossainmd744@gmail.com', 'Car Rental Portal');
            $mail->addAddress($email, $fname);

            $mail->isHTML(true);
            $mail->Subject = 'Registration Successful';
            $mail->Body = '
                <table width="100%" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
                  <tr>
                    <td align="center">
                      <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                        <tr style="background-color: #007bff;">
                          <td style="padding: 20px; text-align: center; color: #ffffff; font-size: 24px;">
                            🚗 Car Rental Portal
                          </td>
                        </tr>
                        <tr>
                          <td style="padding: 30px; color: #333333;">
                            <h2 style="margin-top: 0;">Hello ' . $fname . ',</h2>
                            <p>Thank you for registering with <strong>Car Rental Portal</strong>! We\'re excited to have you on board.</p>
                            <p>You can now log in to your account and start booking your favorite vehicles.</p>
                            <p style="text-align: center; margin: 30px 0;">
                              <a href="http://yourdomain.com/index.php#loginform" style="background-color: #28a745; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 5px; display: inline-block;">Login to Your Account</a>
                            </p>
                            <p>If you didn\'t sign up for this account, you can ignore this email.</p>
                            <p>Best regards,<br>Car Rental Team</p>
                          </td>
                        </tr>
                        <tr style="background-color: #f1f1f1;">
                          <td style="padding: 15px; text-align: center; font-size: 12px; color: #888888;">
                            &copy; ' . date("Y") . ' Car Rental Portal. All rights reserved.
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>';

            $mail->send();
        } catch (Exception $e) {
            // You may log the error if needed
        }

        echo "<script>alert('Registration successful. Now you can login');</script>";
    }
    else 
    {
        echo "<script>alert('Something went wrong. Please try again');</script>";
    }
}
?>



<script>
function checkAvailability() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'emailid='+$("#emailid").val(),
type: "POST",
success:function(data){
$("#user-availability-status").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}
</script>

<div class="modal fade" id="signupform">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Sign Up</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="signup_wrap">
            <div class="col-md-12 col-sm-6">
              <form  method="post" name="signup">
                <div class="form-group">
                  <input type="text" class="form-control" name="fullname" placeholder="Full Name" required="required">
                </div>
                      <div class="form-group">
                  <input type="text" class="form-control" name="mobileno" placeholder="Mobile Number" maxlength="10" required="required">
                </div>
                <div class="form-group">
                  <input type="email" class="form-control" name="emailid" id="emailid" onBlur="checkAvailability()" placeholder="Email Address" required="required">
                   <span id="user-availability-status" style="font-size:12px;"></span> 
                </div>
                <div class="form-group">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Password" required="required">
                </div>
          
                <div class="form-group checkbox">
                  <input type="checkbox" id="terms_agree" required="required" checked="">
                  <label for="terms_agree">I Agree with <a href="#">Terms and Conditions</a></label>
                </div>
                <div class="form-group">
                  <input type="submit" value="Sign Up" name="signup" id="submit" class="btn btn-block">
                </div>
              </form>
            </div>
            
          </div>
        </div>
      </div>
      <div class="modal-footer text-center">
        <p>Already got an account? <a href="#loginform" data-toggle="modal" data-dismiss="modal">Login Here</a></p>
      </div>
    </div>
  </div>
</div>