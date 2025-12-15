<style>
/* Sidebar Styles */
.ts-sidebar {
    width: 250px;
    background: #2c3e50;
    min-height: 100vh;
    color: #ecf0f1;
    position: fixed;
    overflow-y: auto;
}

.ts-sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.ts-sidebar-menu li {
    border-bottom: 1px solid #34495e;
}

.ts-sidebar-menu li a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #ecf0f1;
    text-decoration: none;
    transition: background 0.3s;
}

.ts-sidebar-menu li a i {
    margin-right: 12px;
    min-width: 20px;
    text-align: center;
}

.ts-sidebar-menu li a:hover,
.ts-sidebar-menu li.active > a {
    background: #1abc9c;
    color: #fff;
}

.ts-sidebar-menu ul {
    list-style: none;
    padding-left: 20px;
    display: none;
    background: #34495e;
}

.ts-sidebar-menu li.open > ul {
    display: block;
}

.ts-label {
    font-size: 1rem;
    font-weight: bold;
    padding: 16px 20px;
    background: #1a252f;
    text-transform: uppercase;
}
</style>

<script>
// Sidebar Dropdown Toggle Script
document.addEventListener('DOMContentLoaded', function () {
    const items = document.querySelectorAll('.ts-sidebar-menu > li > a[href="#"]');
    items.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const parentLi = this.parentElement;
            parentLi.classList.toggle('open');
        });
    });
});
</script>

<nav class="ts-sidebar">
    <ul class="ts-sidebar-menu">
        <li class="ts-label">Main</li>
        <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>

        <li>
            <a href="#"><i class="fa fa-files-o"></i> Brands</a>
            <ul>
                <li><a href="create-brand.php"><i class="fa fa-plus-circle"></i> Create Brand</a></li>
                <li><a href="manage-brands.php"><i class="fa fa-tasks"></i> Manage Brands</a></li>
            </ul>
        </li>

        <li>
            <a href="#"><i class="fa fa-car"></i> Vehicles</a>
            <ul>
                <li><a href="post-avehical.php"><i class="fa fa-plus"></i> Post a Vehicle</a></li>
                <li><a href="manage-vehicles.php"><i class="fa fa-cogs"></i> Manage Vehicles</a></li>
            </ul>
        </li>

        <li>
		  <a href="#"><i class="fa fa-bell"></i> Bookings 
		    <span id="sidebar-badge" class="badge badge-danger" style="float:right; display:none;">0</span>
		  </a>
		  <ul>
		    <li><a href="new-bookings.php"><i class="fa fa-bell"></i> New</a></li>
		    <li><a href="confirmed-bookings.php"><i class="fa fa-check-circle"></i> Confirmed</a></li>
		    <li><a href="canceled-bookings.php"><i class="fa fa-times-circle"></i> Canceled</a></li>
		  </ul>
		</li>


        <li>
            <a href="#"><i class="fa fa-bar-chart"></i> Reports</a>
            <ul>
                <li><a href="daily-report.php"><i class="fa fa-calendar"></i> Daily Booking Report</a></li>
                <li><a href="monthly-report.php"><i class="fa fa-calendar-o"></i> Monthly Booking Report</a></li>
                <li><a href="all-booking-report.php"><i class="fa fa-calendar-o"></i> All Booking Report</a></li>
                <li><a href="vehicle-report.php"><i class="fa fa-car"></i> Vehicle Booking Report</a></li>
                <li><a href="customer-report.php"><i class="fa fa-user"></i> Customer Booking Report</a></li>
            </ul>
        </li>
        <li>
          <a href="due-payments.php"><i class="fa fa-credit-card"></i> Handle Due Payments
            <span id="sidebar-due-badge" class="badge badge-danger" style="float:right; display:none;">0</span>
          </a>
        </li>


        <li><a href="testimonials.php"><i class="fa fa-table"></i> Manage Testimonials</a></li>
        <li><a href="manage-conactusquery.php"><i class="fa fa-envelope-open"></i> Contact Queries</a></li>
        <li><a href="reg-users.php"><i class="fa fa-users"></i> Registered Users</a></li>
        <li><a href="manage-pages.php"><i class="fa fa-file-text-o"></i> Manage Pages</a></li>
        <li><a href="update-contactinfo.php"><i class="fa fa-phone"></i> Update Contact Info</a></li>
        <li><a href="manage-subscribers.php"><i class="fa fa-bell-o"></i> Manage Subscribers</a></li>
    </ul>
</nav>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function fetchNewBookingCount() {
  $.ajax({
    url: 'check-booking-notification.php',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      const count = data.new_bookings;
      if (count > 0) {
        $('#sidebar-badge').text(count).show();
      } else {
        $('#sidebar-badge').hide();
      }
    },
    error: function() {
      console.error("Could not fetch notification count.");
    }
  });
}
// Update both badges every 10 seconds
setInterval(fetchNewBookingCount, 10000);


$(document).ready(function() {
  fetchNewBookingCount();
 
});
</script>

<script>
function fetchDuePaymentCount() {
  $.ajax({
    url: 'check-due-notification.php',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      const dueCount = data.due_payments;
      if (dueCount > 0) {
        $('#sidebar-due-badge').text(dueCount).show();
      } else {
        $('#sidebar-due-badge').hide();
      }
    },
    error: function() {
      console.error("Could not fetch due payment count.");
    }
  });
}

setInterval(fetchDuePaymentCount, 10000); // Every 10 seconds

$(document).ready(function() {
  fetchDuePaymentCount();
});
</script>
