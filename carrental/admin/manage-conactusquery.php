<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) {	
    header('location:index.php');
} else {
    if(isset($_REQUEST['eid'])) {
        $eid = intval($_GET['eid']);
        $status = 1;
        $sql = "UPDATE tblcontactusquery SET status=:status WHERE id=:eid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':eid', $eid, PDO::PARAM_STR);
        $query->execute();
    }
?>

<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <title>Admin | Manage Contact Queries</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .page-title {
            font-weight: bold;
            font-size: 24px;
            margin: 20px 0;
            color: #343a40;
        }
        .panel {
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.07);
            background: #fff;
            border: none;
        }
        .panel-heading {
            background-color: #343a40;
            color: #fff;
            padding: 14px 20px;
            font-size: 16px;
            border-radius: 12px 12px 0 0;
        }
        .table thead {
            background-color: #343a40;
            color: #ffffff;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .badge-read {
            background-color: #28a745;
            color: #fff;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }
        .succWrap, .errorWrap {
            padding: 10px 20px;
            margin: 20px 0;
            border-left: 5px solid;
            background: #ffffff;
            border-radius: 5px;
        }
        .succWrap { border-color: #28a745; color: #155724; }
        .errorWrap { border-color: #dc3545; color: #721c24; }
        a.badge-pending:hover {
            text-decoration: none;
            opacity: 0.8;
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
                        <h2 class="page-title">📬 Manage Contact Us Queries</h2>

                        <div class="panel">
                            <div class="panel-heading">User Queries</div>
                            <div class="panel-body">

                                <div class="table-responsive">
                                    <table id="zctb" class="display table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Contact No</th>
                                                <th>Message</th>
                                                <th>Posting Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM tblcontactusquery";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt = 1;
                                            if($query->rowCount() > 0) {
                                                foreach($results as $result) {
                                            ?>
                                            <tr>
                                                <td><?php echo htmlentities($cnt); ?></td>
                                                <td><?php echo htmlentities($result->name); ?></td>
                                                <td><?php echo htmlentities($result->EmailId); ?></td>
                                                <td><?php echo htmlentities($result->ContactNumber); ?></td>
                                                <td><?php echo htmlentities($result->Message); ?></td>
                                                <td><?php echo htmlentities($result->PostingDate); ?></td>
                                                <td>
                                                    <?php if($result->status == 1) { ?>
                                                        <span class="badge badge-read">Read</span>
                                                    <?php } else { ?>
                                                        <a href="manage-conactusquery.php?eid=<?php echo htmlentities($result->id); ?>" class="badge badge-pending" onclick="return confirm('Mark this as read?')">Pending</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php $cnt++; }} ?>
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

    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>

<?php } ?>
