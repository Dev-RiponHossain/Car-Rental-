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
$sql = "delete from tblbrands  WHERE id=:id";
$query = $dbh->prepare($sql);
$query -> bindParam(':id',$id, PDO::PARAM_STR);
$query -> execute();
$msg="Page data updated  successfully";

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
	
	<title>Car Rental Portal |Admin Manage Brands   </title>

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
		./* Container এর জন্য সেন্টারিং */
.dataTables_wrapper {
    max-width: 1000px;
    margin: 30px auto;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* টেবিল হেডার স্টাইল */
table#zctb thead tr {
    background-color: #007bff; /* ব্লু কালার */
    color: white;
    font-weight: 600;
    font-size: 14px;
    letter-spacing: 0.05em;
}

/* টেবিল বডি রো */
table#zctb tbody tr {
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: background-color 0.3s ease;
}

table#zctb tbody tr:hover {
    background-color: #e6f0ff; /* হোভার এ হালকা নীল রঙ */
}

/* টেবিল সেল প্যাডিং ও বর্ডার */
table#zctb tbody td {
    padding: 15px 20px;
    vertical-align: middle;
    border-top: none !important;
    color: #333;
}

/* প্রথম কলামের কাউন্টার বড় আর হাইলাইটেড */
table#zctb tbody td:first-child {
    font-weight: 700;
    color: #007bff;
}

/* Action বাটনের জন্য */
.action-btn {
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    color: white;
    display: inline-block;
}

.action-confirm {
    background-color: #28a745;
    box-shadow: 0 3px 6px rgba(40,167,69,0.4);
}

.action-confirm:hover {
    background-color: #218838;
    box-shadow: 0 5px 15px rgba(33,136,56,0.6);
    text-decoration: none;
    color: white;
}

.action-cancel {
    background-color: #dc3545;
    box-shadow: 0 3px 6px rgba(220,53,69,0.4);
}

.action-cancel:hover {
    background-color: #c82333;
    box-shadow: 0 5px 15px rgba(200,35,51,0.6);
    text-decoration: none;
    color: white;
}

/* Success/Error message box */
.succWrap, .errorWrap {
    max-width: 1000px;
    margin: 20px auto;
    padding: 12px 20px;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.succWrap {
    background-color: #d4edda;
    border-left: 6px solid #28a745;
    color: #155724;
}

.errorWrap {
    background-color: #f8d7da;
    border-left: 6px solid #dc3545;
    color: #721c24;
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

						<h2 class="page-title">Manage Brands</h2>

						<!-- Zero Configuration Table -->
						<div class="panel panel-default">
							<div class="panel-heading">Listed  Brands</div>
							<div class="panel-body">
							<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
										<th>#</th>
												<th>Brand Name</th>
											<th>Creation Date</th>
											<th>Updation date</th>
										
											<th>Action</th>
										</tr>
									</thead>
									
									<tbody>

									<?php $sql = "SELECT * from  tblbrands ";
										$query = $dbh -> prepare($sql);
										$query->execute();
										$results=$query->fetchAll(PDO::FETCH_OBJ);
										$cnt=1;
										if($query->rowCount() > 0)
										{
										foreach($results as $result)
										{				?>	
										<tr>
											<td><?php echo htmlentities($cnt);?></td>
											<td><?php echo htmlentities($result->BrandName);?></td>
											<td><?php echo htmlentities($result->CreationDate);?></td>
											<td><?php echo htmlentities($result->UpdationDate);?></td>
											<td>
											    <a href="edit-brand.php?id=<?php echo $result->id;?>" class="action-btn action-confirm">
											        <i class="fa fa-pencil"></i> Edit
											    </a>
											    <a href="manage-brands.php?del=<?php echo $result->id;?>" onclick="return confirm('Do you want to delete?');" class="action-btn action-cancel">
											        <i class="fa fa-trash"></i> Delete
											    </a>
											</td>

										</tr>
										<?php $cnt=$cnt+1; }} ?>
										
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
