<?php

// Initialize server side session to recognize that browser instance (sets a user key in user's browser)
session_start();

// Check if the user is logged in as an Admin, if not then redirect to login/welcome page
if( !isset($_SESSION["loggedin"])) {
    header("location: ../login.php") ;
    exit;
} elseif ($_SESSION["loggedin"] != "A"){
    header("location: ../Welcome.html") ;
    exit;
}

// Empty all variables that will be used
$customer_id = $kit_type = $kit_qty = $kit_price = "";
$party_date = $party_time = $party_address = $party_city = $party_state = "";
$err_msg = "";

// Create the database connection object
require_once "DB_Conn.php";

// Check existence of id parameter before processing further
if (isset($_GET["BookID"]) && !empty(trim($_GET["BookID"]))) {
    // Get URL parameter
    $book_id = trim($_GET["BookID"]);

    $sql = "SELECT * FROM BOOKING WHERE BookingID = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $param_bookid);
        $param_bookid = $book_id;
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $customer_id = $row["CustomerID"];
                $kit_type = $row["KitID"];
                $kit_qty = $row["QtyKits"];
                $kit_price = $row["PriceEa"];
                $party_state = $row["PartyState"];
                list($party_date, $party_time) = explode(' ',$row["PartyDate"]);
                $party_city = $row["PartyCity"];
                $party_address = $row["PartyStreetAddr"];
            } else {
                // URL doesn't contain valid id. Redirect to error page
                header("location: dashboard.php");
                exit();
            }
        }
        $stmt->close();
    }
} else {
    // URL doesn't contain valid id. Redirect to error page
    header("location: dashboard.php");
    exit();
}

?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MakerFun Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-robot"></i>
            </div>
            <div class="sidebar-brand-text mx-3">MakerFun Admin</div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item active">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Interface
        </div>


        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Addons
        </div>

        <!-- Nav Item - Charts -->
        <li class="nav-item">
            <a class="nav-link" href="charts.php">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Charts</span></a>
        </li>

        <!-- Nav Item - User Tables -->
        <li class="nav-item">
            <a class="nav-link" href="ViewUser.php">
                <i class="fas fa-fw fa-table"></i>
                <span>Users</span></a>
        </li>
        <!-- Nav Item - Booking Tables -->
        <li class="nav-item">
            <a class="nav-link" href="BookingView.php">
                <i class="fas fa-fw fa-table"></i>
                <span>Bookings</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Topbar Search -->
                <form action="https://duckduckgo.com/?" method="get" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                    <li class="nav-item dropdown no-arrow d-sm-none">
                        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-search fa-fw"></i>
                        </a>
                        <!-- Dropdown - Messages -->
                        <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                            <form class="form-inline mr-auto w-100 navbar-search">
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button">
                                            <i class="fas fa-search fa-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>


                    <! // Use PHP to print the USER NAME next to the icon >
                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <?php echo $_SESSION["FName"]." ". $_SESSION["LName"] ?> </span>
                            <img class="img-profile rounded-circle" src="img/Hum.jpg">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profile
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Settings
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                Activity Log
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>

                </ul>

            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <!--            <div class="card o-hidden border-0 shadow-lg my-5">-->
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-1 col-md-6 col d-none d-lg-block"></div>
                            <!--                        <div class="col-lg-3 d-none d-lg-block"></div>-->
                            <div class="col-lg-10">
                                <div class="p-5">


                                    <div class="">
                                        <h1 class="h4 text-gray-900 mb-4">Booking Details</h1>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3 mb-3 mb-sm-0">
                                            <label>Customer ID:</label>
                                            <span class="form-control-static" ><b><?php echo $customer_id; ?></b></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label>Kit Type:</label>
                                            <span class="form-control-static" ><b><?php echo $kit_type; ?></b></span>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Kit Qty:</label>
                                            <span class="form-control-static" ><b><?php echo $kit_qty; ?></b></span>
                                        </div>
                                        <div class="col-sm-3">
                                            <label for="KitPrice">Kit Price:</label>
                                            <span class="form-control-static" ><b><?php echo $kit_price; ?></b></span>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="col-sm-6 mb-3 mb-sm-0 ">
                                            <label>Date of Party:</label>
                                            <span class="form-control-static" ><b><?php echo $party_date; ?></b></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Time:</label>
                                            <span class="form-control-static" ><b><?php echo $party_time; ?></b></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-5 ">
                                            <label>Address:</label>
                                            <span class="form-control-static" ><b><?php echo $party_address; ?></b></span>
                                        </div>
                                        <div class="col-sm-3 mb-3 mb-sm-0">
                                            <label>City:</label>
                                            <span class="form-control-static" ><b><?php echo $party_city; ?></b></span>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>State:</label>
                                            <span class="form-control-static" ><b><?php echo $party_state; ?></b></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <a href="BookingView.php" class="btn btn-primary btn-user btn-block">
                                            Back
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Your Website 2019</span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">�</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="js/demo/chart-area-demo.js"></script>
<script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>