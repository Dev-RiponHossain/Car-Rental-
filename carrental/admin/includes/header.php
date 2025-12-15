<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style type="text/css">
.ts-account > ul {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: #fff;
  min-width: 180px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.15);
  border-radius: 8px;
  padding: 12px 0;
  display: none;
  z-index: 1000;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.ts-account:hover > ul {
  display: block;
}

.ts-account > ul li {
  list-style: none;
  margin: 6px 0; 
}

.ts-account > ul li a {
  display: block;
  padding: 12px 20px;
  color: #333;
  text-decoration: none;
  font-size: 15px;
  border-radius: 6px;
  transition: background-color 0.25s ease, color 0.25s ease;
}

.ts-account > ul li a:hover {
  background-color: #34495e; 
  color: #ecf0f1; 
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(52, 73, 94, 0.4);
}
</style>


<div class="brand clearfix p-0" style="display: flex; align-items: center; justify-content: space-between; background-color: #2c3e50; ">

    <!-- Left section: Logo -->
    <span class="menu-btn" style="color: white; font-size: 20px;"><i class="fa fa-bars"></i></span>
    <div style="flex: 1; display: flex; justify-content: flex-start; min-width: 30%;">
        <a href="dashboard.php" style="display: flex; align-items: center; text-decoration: none;">
            <img src="../assets/images/logo.png" style="height: 70px; width: 100%;">
        </a>
    </div>

    <!-- Center section: Admin Panel text with icon -->
    <div style="flex: 1; display: flex; justify-content: center; align-items: center;">
        <span style="font-size: 24px; font-weight: bold; color: white;">
            <i class="fa fa-tachometer" style="margin-right: 8px;"></i> ADMIN PANEL
        </span>
    </div>

    <!-- Right section: Account menu -->
    <div style="flex: 1; display: flex; justify-content: flex-end; align-items: center;">
        <ul class="ts-profile-nav" style="margin: 0; padding: 0; list-style: none;">
            <li class="ts-account" style="position: relative;">
                <a href="#" style="display: flex; align-items: center; color: white; text-decoration: none;">
                    <img src="img/ripon.jpg" class="ts-avatar hidden-side" alt="" style="height: 40px; width: 40px; border-radius: 50%; margin-right: 8px;">
                    Account <i class="fa fa-angle-down hidden-side" style="margin-left: 5px;"></i>
                </a>
                <ul style="position: absolute; background-color: white; padding: 10px; margin-top: 5px; list-style: none; display: none;">
                    <li><a href="change-password.php">Change Password</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const accountLink = document.querySelector(".ts-account > a");
    const dropdownMenu = document.querySelector(".ts-account ul");

    accountLink.addEventListener("click", function (e) {
      e.preventDefault();
      // Toggle dropdown menu
      if (dropdownMenu.style.display === "block") {
        dropdownMenu.style.display = "none";
      } else {
        dropdownMenu.style.display = "block";
      }
    });

    // Optional: Click outside to close dropdown
    document.addEventListener("click", function (e) {
      if (!accountLink.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownMenu.style.display = "none";
      }
    });
  });
</script>
