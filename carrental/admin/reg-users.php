<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{	
    header('location:index.php');
}
else{
    if(isset($_GET['del']))
    {
        $id=$_GET['del'];
        $sql = "delete from tblbrands WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query -> bindParam(':id',$id, PDO::PARAM_STR);
        $query -> execute();
        $msg="Page data updated successfully";
    }
?>

<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
	<meta name="description" content="" />
	<meta name="author" content="" />
	<meta name="theme-color" content="#3e454c" />

	<title>Car Rental Portal | Admin Manage testimonials</title>

	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css" />
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css" />
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css" />
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css" />
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css" />
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css" />
	<!-- Admin Style -->
	<link rel="stylesheet" href="css/style.css" />

<style>
.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}


/* Page title styling */


.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #34495e;
    margin-bottom: 30px;
    position: relative;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    text-align: center;
}

.page-title::after {
    content: "";
    display: block;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #2980b9, #8e44ad);
    border-radius: 2px;
    margin: 8px auto 0; /* centers horizontally and adds top margin */
}

/* Table professional design */
table.table thead th {
    font-size: 1.1rem;  /* larger header font */
    padding: 16px 20px;
    background-color: #000; /* Black background */
    color: #fff;
    border-bottom: 2px solid #222;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}


table.table tbody td {
    font-size: 1rem; /* larger body font */
    padding: 14px 20px;
    color: #2c3e50;
    font-weight: 500;
    border-bottom: 1px solid #ddd;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

table.table tbody tr {
    min-height: 60px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

table.table tbody tr:hover {
    background-color: #d6e0ff;
    color: #1a237e;
    font-weight: 600;
    cursor: pointer;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
        margin-bottom: 20px;
    }
    table.table thead th,
    table.table tbody td {
        font-size: 0.95rem;
        padding: 12px 15px;
    }
}
</style>

</head>

<body>
	<?php include('includes/header.php'); ?>

	<div class="ts-main-content">
		<?php include('includes/leftbar.php'); ?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">

						<h3 class="page-title">Registered Users</h3>

						<!-- Zero Configuration Table -->
						<div class="panel panel-default">
							<div class="panel-heading">Reg Users</div>
							<div class="panel-body">
							<?php if($error){?>
                                <div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div>
                            <?php } else if($msg){?>
                                <div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div>
                            <?php } ?>
								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Name</th>
											<th>Email</th>
											<th>Contact no</th>
											<th>DOB</th>
											<th>Address</th>
											<th>City</th>
											<th>Country</th>
											<th>Reg Date</th>
										</tr>
									</thead>
									<tbody>

									<?php 
                                    $sql = "SELECT * FROM tblusers";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results=$query->fetchAll(PDO::FETCH_OBJ);
                                    $cnt=1;
                                    if($query->rowCount() > 0) {
                                        foreach($results as $result) { ?>	
										<tr>
											<td><?php echo htmlentities($cnt);?></td>
											<td><?php echo htmlentities($result->FullName);?></td>
											<td><?php echo htmlentities($result->EmailId);?></td>
											<td><?php echo htmlentities($result->ContactNo);?></td>
											<td><?php echo htmlentities($result->dob);?></td>
											<td><?php echo htmlentities($result->Address);?></td>
											<td><?php echo htmlentities($result->City);?></td>
											<td><?php echo htmlentities($result->Country);?></td>
											<td><?php echo htmlentities($result->RegDate);?></td>
										</tr>
									<?php $cnt++; } } ?>
										
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
