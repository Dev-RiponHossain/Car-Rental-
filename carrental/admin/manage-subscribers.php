<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
} else {
    if(isset($_GET['del'])) {
        $id = $_GET['del'];
        $sql = "DELETE FROM tblsubscribers WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->execute();
        $msg = "Subscriber deleted successfully";
    }
?>

<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <title>Admin | Manage Subscribers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Links -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
        }
        .page-title {
            font-weight: bold;
            color: #343a40;
            margin: 20px 0;
        }
        .panel {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            background: #fff;
            border: none;
        }
        .panel-heading {
            background: #343a40;
            color: white;
            font-size: 16px;
            padding: 12px 20px;
            border-radius: 12px 12px 0 0;
        }
        .table thead {
            background-color: #343a40;
            color: #fff;
        }
        .table th, .table td {
            vertical-align: middle !important;
            text-align: center;
        }
        .table td {
            background-color: #fff;
        }
        .fa-close {
            color: #dc3545;
            cursor: pointer;
        }
        .fa-close:hover {
            color: #a71d2a;
        }
        .succWrap, .errorWrap {
            padding: 10px 20px;
            margin-bottom: 20px;
            border-left: 5px solid;
            border-radius: 5px;
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .succWrap {
            border-color: #28a745;
            color: #155724;
        }
        .errorWrap {
            border-color: #dc3545;
            color: #721c24;
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

                        <h2 class="page-title">📧 Manage Subscribers</h2>

                        <div class="panel">
                            <div class="panel-heading">Subscriber List</div>
                            <div class="panel-body">
                                <?php if($error){ ?>
                                    <div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?></div>
                                <?php } else if($msg){ ?>
                                    <div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?></div>
                                <?php } ?>

                                <div class="table-responsive">
                                    <table id="zctb" class="display table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Email Address</th>
                                                <th>Subscribed On</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $sql = "SELECT * FROM tblsubscribers";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if($query->rowCount() > 0) {
                                                foreach($results as $result) {
                                            ?>
                                            <tr>
                                                <td><?php echo htmlentities($cnt); ?></td>
                                                <td><?php echo htmlentities($result->SubscriberEmail); ?></td>
                                                <td><?php echo htmlentities($result->PostingDate); ?></td>
                                                <td>
                                                    <a href="manage-subscribers.php?del=<?php echo $result->id;?>" onclick="return confirm('Are you sure to delete this subscriber?')" title="Delete">
                                                        <i class="fa fa-close"></i>
                                                    </a>
                                                </td>
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
    </div>

    <!-- JS Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>

<?php } ?>
