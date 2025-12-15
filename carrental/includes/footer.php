<?php
if(isset($_POST['emailsubscibe']))
{
$subscriberemail=$_POST['subscriberemail'];
$sql ="SELECT SubscriberEmail FROM tblsubscribers WHERE SubscriberEmail=:subscriberemail";
$query= $dbh -> prepare($sql);
$query-> bindParam(':subscriberemail', $subscriberemail, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query -> rowCount() > 0)
{
echo "<script>alert('Already Subscribed.');</script>";
}
else{
$sql="INSERT INTO  tblsubscribers(SubscriberEmail) VALUES(:subscriberemail)";
$query = $dbh->prepare($sql);
$query->bindParam(':subscriberemail',$subscriberemail,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
echo "<script>alert('Subscribed successfully.');</script>";
}
else 
{
echo "<script>alert('Something went wrong. Please try again');</script>";
}
}
}
?>

<footer class="modern-footer">
  <div class="footer-wave">
    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
      <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="currentColor"></path>
      <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="currentColor"></path>
      <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="currentColor"></path>
    </svg>
  </div>
  
  <div class="footer-top">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="footer-about">
            <h6><i class="fas fa-car-alt"></i> Car Rental Portal</h6>
            <p>Your trusted partner for premium car rental services. We offer the best vehicles at competitive prices with exceptional customer service.</p>
            <div class="trust-badges">
              <span class="badge"><i class="fas fa-shield-alt"></i> Secure</span>
              <span class="badge"><i class="fas fa-thumbs-up"></i> Reliable</span>
              <span class="badge"><i class="fas fa-star"></i> Rated 4.9/5</span>
            </div>
          </div>
        </div>
        
        <div class="col-md-2">
          <h6>Quick Links</h6>
          <ul class="footer-links">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="car-listing.php"><i class="fas fa-car"></i> Vehicles</a></li>
            <li><a href="contact-us.php"><i class="fas fa-envelope"></i> Contact</a></li>
            <li><a href="page.php?type=aboutus"><i class="fas fa-info-circle"></i> About Us</a></li>
            <li><a href="admin/"><i class="fas fa-lock"></i> Admin Login</a></li>
          </ul>
        </div>
  
        <div class="col-md-2">
          <h6>Legal</h6>
          <ul class="footer-links">
            <li><a href="page.php?type=privacy"><i class="fas fa-user-shield"></i> Privacy Policy</a></li>
            <li><a href="page.php?type=terms"><i class="fas fa-file-contract"></i> Terms of Use</a></li>
            <li><a href="page.php?type=faqs"><i class="fas fa-question-circle"></i> FAQs</a></li>
            <li><a href="my-booking.php"><i class="fas fa-credit-card"></i> Payment Methods</a></li>
            <li><a href="#"><i class="fas fa-shield-alt"></i> Security</a></li>
          </ul>
        </div>
  
        <div class="col-md-4">
          <h6>Newsletter</h6>
          <div class="newsletter-box">
            <p>Subscribe to get special offers, free giveaways, and once-in-a-lifetime deals.</p>
            <form method="post" class="newsletter-form">
              <div class="input-group">
                <input type="email" name="subscriberemail" class="form-control" placeholder="Your email address" required>
                <button type="submit" name="emailsubscibe" class="btn-subscribe">
                  <i class="fas fa-paper-plane"></i>
                </button>
              </div>
            </form>
            <div class="app-download">
              <p>Download Our App:</p>
              <a href="#" class="app-btn">
                <i class="fab fa-apple"></i> App Store
              </a>
              <a href="#" class="app-btn">
                <i class="fab fa-google-play"></i> Play Store
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
         <p class="copyright">&copy; <?php echo date("Y"); ?>
        Car Rental Portal. All Rights Reserved.</p>
        </div>
        <div class="col-md-6">
          <div class="social-links">
            <a href="#" class="social-icon" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-icon" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="social-icon" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="back-to-top">
    <a href="#" id="back-to-top" title="Back to top"><i class="fas fa-arrow-up"></i></a>
  </div>
</footer>

<style>
.modern-footer {
  background: linear-gradient(135deg, #2c3e50 0%, #1a1a2e 100%);
  color: #fff;
  position: relative;
  padding-top: 80px;
  margin-top: 80px;
}

.footer-wave {
  position: absolute;
  top: -60px;
  left: 0;
  width: 100%;
  height: 60px;
  color: #fff;
  z-index: 1;
}

.footer-top {
  padding: 60px 0 40px;
  position: relative;
  z-index: 2;
}

.footer-about h6 {
  font-size: 20px;
  margin-bottom: 20px;
  color: #fff;
  font-weight: 600;
}

.footer-about h6 i {
  margin-right: 10px;
  color: #4ecca3;
}

.footer-about p {
  color: #b8b8b8;
  margin-bottom: 20px;
  line-height: 1.6;
}

.trust-badges .badge {
  display: inline-block;
  background: rgba(255,255,255,0.1);
  padding: 5px 10px;
  border-radius: 20px;
  margin-right: 8px;
  margin-bottom: 8px;
  font-size: 12px;
  color: #ddd;
}

.trust-badges .badge i {
  margin-right: 5px;
}

.footer-links {
  list-style: none;
  padding: 0;
}

.footer-links li {
  margin-bottom: 12px;
}

.footer-links a {
  color: #b8b8b8;
  text-decoration: none;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
}

.footer-links a i {
  margin-right: 8px;
  width: 20px;
  text-align: center;
  color: #4ecca3;
}

.footer-links a:hover {
  color: #4ecca3;
  padding-left: 5px;
}

.newsletter-box {
  background: rgba(255,255,255,0.05);
  padding: 25px;
  border-radius: 10px;
}

.newsletter-box p {
  color: #b8b8b8;
  margin-bottom: 20px;
  line-height: 1.6;
}

.input-group {
  display: flex;
  margin-bottom: 20px;
}

.input-group input {
  flex: 1;
  padding: 12px 15px;
  border: none;
  border-radius: 5px 0 0 5px;
  background: rgba(255,255,255,0.1);
  color: #fff;
}

.input-group input::placeholder {
  color: #ccc;
}

.btn-subscribe {
  background: #4ecca3;
  color: #fff;
  border: none;
  padding: 0 20px;
  border-radius: 0 5px 5px 0;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-subscribe:hover {
  background: #3cb391;
}

.app-download {
  margin-top: 20px;
}

.app-download p {
  margin-bottom: 10px;
  color: #b8b8b8;
}

.app-btn {
  display: inline-block;
  background: rgba(255,255,255,0.1);
  color: #fff;
  padding: 8px 15px;
  border-radius: 5px;
  margin-right: 10px;
  margin-bottom: 10px;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 14px;
}

.app-btn i {
  margin-right: 5px;
}

.app-btn:hover {
  background: #4ecca3;
  color: #fff;
}

.footer-bottom {
  background: rgba(0,0,0,0.2);
  padding: 20px 0;
  border-top: 1px solid rgba(255,255,255,0.05);
}

.copyright {
  color: #b8b8b8;
  margin: 0;
  font-size: 14px;
}

.social-links {
  text-align: right;
}

.social-icon {
  display: inline-block;
  width: 36px;
  height: 36px;
  line-height: 36px;
  text-align: center;
  background: rgba(255,255,255,0.1);
  color: #fff;
  border-radius: 50%;
  margin-left: 10px;
  transition: all 0.3s ease;
}

.social-icon:hover {
  background: #4ecca3;
  transform: translateY(-3px);
}

.back-to-top {
  position: fixed;
  bottom: 30px;
  right: 30px;
  z-index: 999;
}

.back-to-top a {
  display: block;
  width: 50px;
  height: 50px;
  line-height: 50px;
  text-align: center;
  background: #4ecca3;
  color: #fff;
  border-radius: 50%;
  font-size: 20px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}

.back-to-top a:hover {
  background: #3cb391;
  transform: translateY(-5px);
}

@media (max-width: 768px) {
  .footer-top {
    padding: 40px 0 20px;
  }
  
  .footer-bottom .copyright,
  .footer-bottom .social-links {
    text-align: center;
  }
  
  .social-links {
    margin-top: 15px;
  }
  
  .newsletter-box {
    margin-top: 30px;
  }
}
</style>

<script>
// Back to top button
jQuery(document).ready(function($){
  $(window).scroll(function(){
    if ($(this).scrollTop() > 300) {
      $('#back-to-top').fadeIn();
    } else {
      $('#back-to-top').fadeOut();
    }
  });
  
  $('#back-to-top').click(function(){
    $('html, body').animate({scrollTop : 0}, 800);
    return false;
  });
});
</script>