<?php
// Start session and determine if user is already logged in. If not, redirect to login page
session_start();

// Check if the user is logged in as an Admin, if not then redirect to login/welcome page
if( !isset($_SESSION["loggedin"])) {
    header("location: ../login.php") ;
    exit;
} elseif ($_SESSION["loggedin"] != "A"){
    header("location: ../Welcome.html") ;
    exit;
}

    // Create the database connection object
    require_once "DB_Conn.php";

// Query DB for KIT USAGE by MONTH
$sql = "SELECT 
    DATE_FORMAT(PartyDate,\"%M %Y\") AS Month, 
    KitID AS Kit,
    ROUND(SUM(B.QtyKits*B.PriceEa),2) AS KitRev,
    SUM(B.QtyKits) AS Qty
FROM BOOKING AS B 
WHERE (DATEDIFF(NOW(), B.PartyDate) < 365) AND  (DATEDIFF(NOW(), B.PartyDate) > 0 )
GROUP BY Month, KitID
ORDER BY KitID ASC, PartyDate ASC";

// Empty variables
$CountKit1 = $CountKit2 = $CountKit3 = $Month = [];
$Kit1Rev = $Kit2Rev = $Kit3Rev = $MonthlyRev = [];

// Break kit usage into usable variables for building tables
if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
        // Pull out the set of 12 and store into Arrays for Months, Kit1 count, and Kit1 revenue
        for ($i = 0; $i < 12; $i++) {
            $row = $result->fetch_array();
            $Month[$i] = $row["Month"];
            $CountKit1[$i] = $row["Qty"];
            $Kit1Rev[$i] = $row["KitRev"];
        }
        // Pull out the second set of 12 and store into Arrays for Kit2 count, and Kit2 revenue
        for ($i = 0; $i < 12; $i++) {
            $row = $result->fetch_array();
            $CountKit2[$i] = $row["Qty"];
            $Kit2Rev[$i] = $row["KitRev"];
        }
        // Pull out the third set of 12 and store into Arrays for Kit3 count, and Kit3 revenue
        for ($i = 0; $i < 12; $i++) {
            $row = $result->fetch_array();
            $CountKit3[$i] = $row["Qty"];
            $Kit3Rev[$i] = $row["KitRev"];
        }
        // Add all three kit revenues back together and store in monthly totals array
        for ($i = 0; $i < 12; $i++) {
            $MonthlyRev[$i] = $Kit1Rev[$i]+$Kit2Rev[$i]+$Kit3Rev[$i];
        }
        // Add all the months together to get total revenue
        $KitRevTot = array_sum($MonthlyRev);
        $result->free();

    }
}


$sql = "SELECT K.KitName AS Name, SUM(B.QtyKits) AS Usage12M
        FROM BOOKING AS B JOIN KIT_TYPE AS K
        ON B.KitID = K.KitID
        WHERE (datediff(now(), B.PartyDate) <=365) AND (datediff(now(), B.PartyDate) >= 0)
        GROUP BY Name";

$KitTypeName = $KitTypeQty = [];
$KitTot12M = 0;

if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
        for ($i = 0; $i < 3; $i++) {
            $row = $result->fetch_array();
            $KitTypeName[$i] = $row["Name"];
            $KitTypeQty[$i] = $row["Usage12M"];
            $KitTot12M += $row["Usage12M"];
        }
        $result->free();
    }
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
                            <span class="mr-2 d-none d-lg-inline text-primary "> <?php echo $_SESSION["FName"]." ". $_SESSION["LName"] ?> </span>
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

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    <a href="ViewNewUser.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-biohazard fa-sm text-white-50"></i> Forbidden Zone!</a>
                </div>

                <!-- Content Row -->
                <div class="row">

                    <!-- Earnings (ANNUAL) Card Example -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Earnings (Annual)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<script>
                                                document.write((<?php echo $KitRevTot ?>).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));

                                            </script></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- UPCOMING BOOKINGS Card Example -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Upcoming Bookings</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php require_once "SQL_FutureBookings.php"?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-birthday-cake fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FUTURE REVENUE Card Example -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Upcoming Revenue</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<script>
                                                document.write((<?php require_once "SQL_FutureRevenue.php"?>).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));

                                            </script>

                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- FUTURE KITS! -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Future Number of Kits</div>
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-auto">
                                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                    <?php require_once "SQL_FutureKits.php"?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>

                <!-- Content Row -->
                <div class="row">


                </div>




                <div class="row">

                    <!-- Area Chart -->
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow mb-4">
                            <!-- Card Header - Dropdown -->
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Previous 12 Month Revenue</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                        <div class="dropdown-header">Dropdown Header:</div>
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#">Something else here</a>




                                    </div>
                                </div>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">


                                <div class="chart-area">
                                    <canvas id="myAreaCh"></canvas>


                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pie Chart -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <!-- Card Header - Dropdown -->
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                        <div class="dropdown-header">Dropdown Header:</div>
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                                <div class="chart-pie pt-4 pb-2">
                                    <canvas id="myPieChart"></canvas>
                                </div>
                                <div class="mt-4 text-center small">
                    <span class="mr-2">
                      <i class="fas fa-circle text-primary"></i> <?php echo $KitTypeName[0] ?>
                    </span>
                                    <span class="mr-2">
                      <i class="fas fa-circle text-success"></i> <?php echo $KitTypeName[1] ?>
                    </span>
                                    <span class="mr-2">
                      <i class="fas fa-circle text-info"></i> <?php echo $KitTypeName[2] ?>
                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Row -->
                <div class="row">

                    <!-- Content Column -->
                    <div class="col-lg-12 mb-4">

                        <!-- Project Card Example -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Kit Popularity by Month</h6>
                            </div>
                            <div class="card-body">

                                <script>
                                    var barChartData = {
                                        labels: [
                                            <?php
                                            for ($i = 0; $i < 11; $i++) {
                                                echo "\"".$Month[$i]."\",";
                                            }
                                            echo "\"".$Month[11]."\"";
                                            ?>
                                        ],
                                        datasets: [
                                            {
                                                label: "BristleBot",
                                                backgroundColor: "#36b9cc",
                                                borderColor: "#36b9cc",
                                                borderWidth: 1,
                                                data: [
                                                    <?php
                                                    for ($i = 0; $i < 11; $i++) {
                                                        echo $CountKit1[$i].",";
                                                    }
                                                    echo $CountKit1[11];
                                                    ?>
                                                ]
                                            },
                                            {
                                                label: "Blinky",
                                                backgroundColor: "#4e73df",
                                                borderColor: "#4e73df",
                                                borderWidth: 1,
                                                data: [
                                                    <?php
                                                    for ($i = 0; $i < 11; $i++) {
                                                        echo $CountKit2[$i].",";
                                                    }
                                                    echo $CountKit2[11];
                                                    ?>
                                                ]
                                            },
                                            {
                                                label: "Robo",
                                                backgroundColor: "#1cc88a",
                                                borderColor: "#1cc88a",
                                                borderWidth: 1,
                                                data: [
                                                    <?php
                                                    for ($i = 0; $i < 11; $i++) {
                                                        echo $CountKit3[$i].",";
                                                    }
                                                    echo $CountKit3[11];
                                                    ?>
                                                ]
                                            }
                                        ]
                                    };

                                    var chartOptions = {
                                        responsive: true,
                                        legend: {
                                            position: "bottom"
                                        },
                                        title: {
                                            display: true
                                            // ,
                                            // text: "Kit Popularity by Month"
                                        },
                                        scales: {
                                            xAxes: [{
                                                barPercentage: .90,
                                                categoryPercentage: 0.5
                                            }],
                                            yAxes: [{
                                                ticks: {
                                                    beginAtZero: true
                                                }
                                            }]
                                        }
                                    }

                                    window.onload = function() {
                                        var ctx = document.getElementById("KitPopularity").getContext("2d");
                                        window.myBar = new Chart(ctx, {
                                            type: "bar",
                                            data: barChartData,
                                            options: chartOptions
                                        });
                                    };


                                </script>


                                <div id="container" style="width: 100%;">
                                    <!--                        <canvas id="canvas" style="display: block; height: 160px; width: 383px;" width="574" height="240" class="chartjs-render-monitor"></canvas>-->
                                    <canvas id="KitPopularity"></canvas>
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
    <script>

        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#858796';

        function number_format(number, decimals, dec_point, thousands_sep) {
            // *     example: number_format(1234.56, 2, ',', ' ');
            // *     return: '1 234,56'
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        // REVENUE BY MONTH CHART Example
        var ctx = document.getElementById("myAreaCh");
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels:  [
                    <?php
                    for ($i = 0; $i < 11; $i++) {
                        echo "\"".$Month[$i]."\",";
                    }
                    echo "\"".$Month[11]."\"";
                    ?>
                ],
                datasets: [{
                    label: "Earnings",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [
                        <?php
                        for ($i = 0; $i < 11; $i++) {
                            echo $MonthlyRev[$i].",";
                        }
                        echo $MonthlyRev[11];
                        ?>
                    ],
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            // Include a dollar sign in the ticks
                            callback: function(value, index, values) {
                                return '$' + number_format(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });

    </script>



    <script >
        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#858796';

        // Pie Chart Example
        var ctx = document.getElementById("myPieChart");
        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [<?php
                    for ($i = 0; $i < 2; $i++) {
                        echo "\"".$KitTypeName[$i]."\",";
                    }
                    echo "\"".$KitTypeName[2]."\"";
                    ?>
                ],
                datasets: [{
                    data: [<?php
                        for ($i = 0; $i < 2; $i++) {
                            echo "\"".$KitTypeQty[$i]."\",";
                        }
                        echo "\"".$KitTypeQty[2]."\"";
                        ?>
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 80,
            },
        });

    </script>

</body>

</html>

