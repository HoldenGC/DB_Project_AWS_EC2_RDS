<?php

// Initialize server side session to recognize that browser instance (sets a user key in user's browser)
session_start();

// Check if the user is logged in as an Admin, if not then redirect to login/welcome page
if (!isset($_SESSION["loggedin"])) {
    header("location: ../login.php");
    exit;
} elseif ($_SESSION["loggedin"] != "A") {
    header("location: ../Welcome.html");
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
                list($party_date, $party_time) = explode(' ', $row["PartyDate"]);
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
    <title>Read Booking Details</title>
</head>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="">
        <h1 class="h4 text-gray-900 mb-4">Booking Details</h1>
    </div>
    <div class="form-group">
        <div class="col-sm-3 mb-3 mb-sm-0">
            <label>Customer ID:</label>
            <span class="form-control-static"><b><?php echo $customer_id; ?></b></span>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-3">
            <label>Kit Type:</label>
            <span class="form-control-static"><b><?php echo $kit_type; ?></b></span>
        </div>
        <div class="col-sm-3">
            <label>Kit Qty:</label>
            <span class="form-control-static"><b><?php echo $kit_qty; ?></b></span>
        </div>
        <div class="col-sm-3">
            <label for="KitPrice">Kit Price:</label>
            <span class="form-control-static"><b><?php echo $kit_price; ?></b></span>
        </div>
    </div>
    <div class="form-group ">
        <div class="col-sm-6 mb-3 mb-sm-0 ">
            <label>Date of Party:</label>
            <span class="form-control-static"><b><?php echo $party_date; ?></b></span>
        </div>
        <div class="col-sm-6">
            <label>Time:</label>
            <span class="form-control-static"><b><?php echo $party_time; ?></b></span>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-5 ">
            <label>Address:</label>
            <span class="form-control-static"><b><?php echo $party_address; ?></b></span>
        </div>
        <div class="col-sm-3 mb-3 mb-sm-0">
            <label>City:</label>
            <span class="form-control-static"><b><?php echo $party_city; ?></b></span>
        </div>
        <div class="col-sm-3">
            <label>State:</label>
            <span class="form-control-static"><b><?php echo $party_state; ?></b></span>
        </div>
    </div>
    <div class="col-sm-3">
        <a href="BookingView.php" class="btn btn-primary btn-user btn-block">
            Back
        </a>
    </div>
</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->
</body>

</html>