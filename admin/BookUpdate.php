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
if(isset($_POST["BookID"]) && !empty($_POST["BookID"])){
    // Get hidden input value
    $book_id = $_POST["BookID"];

    if (empty(trim($_POST["CustID"]))) {
        $err_msg = "Please enter a customer ID";
    } else {
        $customer_id = trim($_POST["CustID"]);
        $sql = "SELECT Email FROM USERS WHERE CustID = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_custid);
            $param_custid = $customer_id;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows < 1) {
                    $err_msg = "Customer ID not found. Please enter a valid one.";
                }
            } else $err_msg = "OOPS! Something Went Wrong. ". $stmt->error;
        }
        $stmt->close();
    }

    if (empty(trim($_POST["KitType"]))) {
        $err_msg = "Please Enter the Kit Type number";
    } else $kit_type = (trim($_POST["KitType"]));

    if (empty(trim($_POST["KitQty"]))) {
        $err_msg = "Please Enter the Number of Kits needed";
    } else $kit_qty = (trim($_POST["KitQty"]));

    if (empty(trim($_POST["KitPrice"]))) {
        $err_msg = "Please Enter the Price of Kits";
    } else $kit_price = (trim($_POST["KitPrice"]));

    if (empty(trim($_POST["partyDate"]))) {
        $err_msg = "Please Enter a Date for the Party";
    } else $party_date = (trim($_POST["partyDate"]));

    if (empty(trim($_POST["partyTime"]))) {
        $err_msg = "Please Enter a Time for the Party";
    } else $party_time = (trim($_POST["partyTime"]));

    if (empty(trim($_POST["StreetAddress"]))) {
        $err_msg = "Please Enter a Street Address for the Party";
    } else $party_address = (trim($_POST["StreetAddress"]));

    if (empty(trim($_POST["City"]))) {
        $err_msg = "Please Enter a City for the Party";
    } else $party_city = (trim($_POST["City"]));

    if (empty(trim($_POST["State"]))) {
        $err_msg = "Please Enter a State for the Party";
    } else $party_state = (trim($_POST["State"]));

    if(empty($err_msg)) {
        $sql = "UPDATE BOOKING set CustomerId = ?, KitID = ?, QtyKits = ?, PriceEa = ?, PartyState = ?, PartyDate = ?, 
                    PartyCity = ?, PartyStreetAddr = ? where BookingID = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("iiidsssss", $param_CustID,$param_KitID, $param_Qty, $param_Price, $param_State,
                $param_Date, $param_City, $param_StreetAddr, $param_bookid);
            $param_bookid = $book_id;
            $param_CustID = $customer_id;
            $param_KitID = $kit_type;
            $param_Qty = $kit_qty;
            $param_Price = $kit_price;
            $param_State = $party_state;
            $param_Date = $party_date." ".$party_time;
            $param_City = $party_city;
            $param_StreetAddr = $party_address;
            if ($stmt->execute()) {
                header("location: BookingView.php");
            } else $err_msg = "OOPS! Something Went Wrong. Could add booking. " . $stmt->error;
            $stmt->close();
        }else $err_msg = "OOPS! Something Went Wrong. ";
    }
    $mysqli->close();
}elseif (isset($_GET["BookID"]) && !empty(trim($_GET["BookID"]))) { // Check existence of id parameter before processing further
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
                header("location: BookingView.php");
                exit();
            }
        }
        $stmt->close();
    }
} else {
    // URL doesn't contain valid id. Redirect to error page
    header("location: BookingView.php");
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


                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Modify Booking</h1>
                                    </div>
                                    <! /////////////////////////////////////////////////////////////////// >
                                    <! // Use special character replacement to guard against SQL injection >
                                    <! // Set the form action to have SUBMIT call this page again by POST  >
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="user" method="post">
                                        <div class="form-group <?php echo (!empty($err_msg)) ? 'text-danger' : ''; ?>">
                                            <span class="help-block "><?php echo $err_msg;?></span>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-3 mb-3 mb-sm-0">
                                                <! // In case an error happened, re-fill any values which were previously entered >
                                                <label for="CustomerID">Customer ID:</label>
                                                <input type="text" name="CustID" class="form-control form-control-user" id="CustomerID"
                                                       placeholder="Customer ID" value="<?php echo $customer_id; ?>">
                                            </div>

                                            <div class="col-sm-3">
                                                <label for="KitType">Kit Type:</label>
                                                <input type="text" name="KitType" class="form-control form-control-user" id="KitType"
                                                       placeholder="Kit Type #" value="<?php echo $kit_type; ?>">
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="KitQty">Kit Qty:</label>
                                                <input type="text" name="KitQty" class="form-control form-control-user" id="KitQty"
                                                       placeholder="Kit Quantity" value="<?php echo $kit_qty; ?>">
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="KitPrice">Kit Price:</label>
                                                <input type="text" name="KitPrice" class="form-control form-control-user" id="KitPrice"
                                                       placeholder="Kit Price" value="<?php echo $kit_price; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <div class="col-sm-6 mb-3 mb-sm-0 ">
                                                <label for="partyDate">Date of Party:</label>
                                                <input type="date" name="partyDate"  class="form-control form-control-user" id="partyDate"
                                                       placeholder="Date of Party" value="<?php echo $party_date; ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="partyTime">Time (HH:MM AM/PM):</label>
                                                <input type="time" name="partyTime"  class="form-control form-control-user" id="partyTime"
                                                       placeholder="Time of Party" value="<?php echo $party_time; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12 ">
                                                <input type="text" name="StreetAddress" class="form-control form-control-user" id="StreetAddress"
                                                       placeholder="Street Address" value="<?php echo $party_address; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <input type="text" name="City" class="form-control form-control-user" id="City"
                                                       placeholder="City" value="<?php echo $party_city; ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="text" name="State" class="form-control form-control-user" id="State"
                                                       placeholder="State" value="<?php echo $party_state; ?>">
                                            </div>
                                        </div>
                                        <input name="BookID" type="hidden" value="<?php echo $book_id; ?>">
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <input type="submit" class="btn btn-primary btn-user btn-block" value="Update Booking">
                                            </div>
                                            <div class="col-sm-6">
                                                <a class="btn btn-secondary btn-user btn-block" href="BookingView.php">Cancel</a>
                                            </div>
                                        </div>
                                    </form>
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
                    <span aria-hidden="true">×</span>
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