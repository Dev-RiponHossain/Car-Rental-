<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{	
    header('location:index.php');
}
else{
if(isset($_REQUEST['eid']))
{
    $eid=intval($_GET['eid']);
    $status="2";
    $sql = "UPDATE tblbooking SET Status=:status WHERE id=:eid";
    $query = $dbh->prepare($sql);
    $query -> bindParam(':status',$status, PDO::PARAM_STR);
    $query-> bindParam(':eid',$eid, PDO::PARAM_STR);
    $query -> execute();

    $msg="Booking Successfully Cancelled";
}

if(isset($_REQUEST['aeid']))
{
    $aeid=intval($_GET['aeid']);
    $status=1;

    $sql = "UPDATE tblbooking SET Status=:status WHERE id=:aeid";
    $query = $dbh->prepare($sql);
    $query -> bindParam(':status',$status, PDO::PARAM_STR);
    $query-> bindParam(':aeid',$aeid, PDO::PARAM_STR);
    $query -> execute();

    $msg="Booking Successfully Confirmed";
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
	
	<title>Car Rental Portal | Admin Manage Bookings</title>

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
	<!-- Admin Style -->
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

		/* Table spacing & card style */
		table.dataTable {
			border-collapse: separate !important;
			border-spacing: 0 15px !important; /* সারির মধ্যে ভেতরে ১৫px স্পেস */
		}

		table.dataTable tbody tr {
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
		}

		/* Center align table data */
		table.dataTable tbody tr td {
			vertical-align: middle;
		}

		/* Stylish action buttons */
		.action-btn {
			display: inline-block;
			padding: 5px 12px;
			border-radius: 4px;
			color: #fff;
			text-decoration: none;
			font-size: 13px;
			transition: background-color 0.3s ease;
		}

		.action-confirm {
			background-color: #28a745; /* Green */
		}
		.action-confirm:hover {
			background-color: #218838;
			color: #fff;
		}

		.action-cancel {
			background-color: #dc3545; /* Red */
		}
		.action-cancel:hover {
			background-color: #c82333;
			color: #fff;
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

						<h2 class="page-title">Manage Bookings</h2>

						<!-- Zero Configuration Table -->
						<div class="panel panel-default">
							<div class="panel-heading">Bookings Info</div>
							<div class="panel-body">
								<?php if($error){?>
									<div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?> </div>
								<?php } else if($msg){?>
									<div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div>
								<?php }?>

								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Name</th>
											<th>Vehicle</th>
											<th>From Date</th>
											<th>To Date</th>
											<th>Message</th>
											<th>Status</th>
											<th>Posting Date</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>

									<?php 
									$sql = "SELECT tblusers.FullName, tblbrands.BrandName, tblvehicles.VehiclesTitle, tblbooking.FromDate, tblbooking.ToDate, tblbooking.message, tblbooking.VehicleId as vid, tblbooking.Status, tblbooking.PostingDate, tblbooking.id  
										FROM tblbooking 
										JOIN tblvehicles ON tblvehicles.id = tblbooking.VehicleId 
										JOIN tblusers ON tblusers.EmailId = tblbooking.userEmail 
										JOIN tblbrands ON tblvehicles.VehiclesBrand = tblbrands.id 
										ORDER BY tblbooking.id DESC";

									$query = $dbh->prepare($sql);
									$query->execute();
									$results = $query->fetchAll(PDO::FETCH_OBJ);
									$cnt = 1;
									if($query->rowCount() > 0)
									{
										foreach($results as $result)
										{ ?>	
											<tr>
												<td><?php echo htmlentities($cnt);?></td>
												<td><?php echo htmlentities($result->FullName);?></td>
												<td><a href="edit-vehicle.php?id=<?php echo htmlentities($result->vid);?>">
													<?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?>
													</a>
												</td>
												<td><?php echo htmlentities($result->FromDate);?></td>
												<td><?php echo htmlentities($result->ToDate);?></td>
												<td><?php echo htmlentities($result->message);?></td>
												<td>
													<?php 
														if($result->Status==0){
															echo 'Not Confirmed yet';
														} else if ($result->Status==1) {
															echo 'Confirmed';
														} else {
															echo 'Cancelled';
														}
													?>
												</td>
												<td><?php echo htmlentities($result->PostingDate);?></td>
												<td>
                                                    <?php if($result->Status == 0) { ?>
                                                        <a href="manage-bookings.php?aeid=<?php echo htmlentities($result->id);?>" 
                                                           onclick="return confirm('Do you really want to Confirm this booking')" 
                                                           class="action-btn action-confirm">Confirm</a> 

                                                        <a href="manage-bookings.php?eid=<?php echo htmlentities($result->id);?>" 
                                                           onclick="return confirm('Do you really want to Cancel this Booking')" 
                                                           class="action-btn action-cancel">Cancel</a>
                                                    <?php } else { ?>
                                                        <!-- Confirmed or Cancelled: no action buttons -->
                                                        <span style="color: gray; font-style: italic;">No actions available</span>
                                                    <?php } ?>
                                                </td>
											</tr>
										<?php 
										$cnt++;
										} 
									} ?>
										
									</tbody>
								</table>

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

</body>

</html>
<?php } ?>
