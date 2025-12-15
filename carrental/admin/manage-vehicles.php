<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
} else {

    if(isset($_REQUEST['del'])) {
        $delid=intval($_GET['del']);
        $sql = "delete from tblvehicles WHERE id=:delid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':delid',$delid, PDO::PARAM_STR);
        $query->execute();
        $msg="Vehicle record deleted successfully";
    }
?>
<!doctype html>
<html lang="en" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Car Rental Portal | Admin Manage Vehicles</title>

    <!-- CSS Links -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            background-color: #f4f6f9;
        }
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #34495e;
            margin-bottom: 30px;
            text-align: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .page-title::after {
            content: "";
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #2980b9, #8e44ad);
            border-radius: 2px;
            margin: 8px auto 0;
        }
        table.table thead th {
            font-size: 1.1rem;
            padding: 16px 20px;
            background: #000;
            color: #fff;
            border-bottom: 2px solid #222;
            text-transform: uppercase;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        table.table tbody td {
            font-size: 1rem;
            padding: 14px 20px;
            color: #2c3e50;
            font-weight: 500;
            border-bottom: 1px solid #ddd;
        }
        table.table tbody tr:hover {
            background-color: #d6e0ff;
            color: #1a237e;
            font-weight: 600;
            cursor: pointer;
        }
        .btn {
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .btn i {
            margin-right: 5px;
        }
        .btn-primary {
            background: linear-gradient(90deg, #2980b9, #3498db);
            border: none;
        }
        .btn-danger {
            background: linear-gradient(90deg, #e74c3c, #c0392b);
            border: none;
        }
        .btn-primary:hover,
        .btn-danger:hover {
            transform: scale(1.05);
            opacity: 0.9;
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
                        <h2 class="page-title">Manage Vehicles</h2>
                        <div class="panel panel-default">
                            <div class="panel-heading">Vehicle Details</div>
                            <div class="panel-body">
                                <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
                                else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
                                <table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Vehicle Title</th>
                                            <th>Brand</th>
                                            <th>Price Per day</th>
                                            <th>Fuel Type</th>
                                            <th>Model Year</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,tblvehicles.FuelType,tblvehicles.ModelYear,tblvehicles.id from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand";
                                        $query = $dbh -> prepare($sql);
                                        $query->execute();
                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt=1;
                                        if($query->rowCount() > 0)
                                        {
                                        foreach($results as $result)
                                        { ?>
                                            <tr>
                                                <td><?php echo htmlentities($cnt);?></td>
                                                <td><?php echo htmlentities($result->VehiclesTitle);?></td>
                                                <td><?php echo htmlentities($result->BrandName);?></td>
                                                <td><?php echo htmlentities($result->PricePerDay);?></td>
                                                <td><?php echo htmlentities($result->FuelType);?></td>
                                                <td><?php echo htmlentities($result->ModelYear);?></td>
                                                <td>
                                                    <a href="edit-vehicle.php?id=<?php echo $result->id; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
                                                    <a href="manage-vehicles.php?del=<?php echo $result->id; ?>" onclick="return confirm('Do you want to delete?');" class="btn btn-sm btn-danger">
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
    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
<?php } ?>
