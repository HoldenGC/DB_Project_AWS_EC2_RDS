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
if (isset($_POST["BookID"]) && !empty($_POST["BookID"])) {
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
            } else $err_msg = "OOPS! Something Went Wrong. " . $stmt->error;
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

    if (empty($err_msg)) {
        $sql = "UPDATE BOOKING set CustomerId = ?, KitID = ?, QtyKits = ?, PriceEa = ?, PartyState = ?, PartyDate = ?, 
                    PartyCity = ?, PartyStreetAddr = ? where BookingID = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("iiidsssss", $param_CustID, $param_KitID, $param_Qty, $param_Price, $param_State,
                $param_Date, $param_City, $param_StreetAddr, $param_bookid);
            $param_bookid = $book_id;
            $param_CustID = $customer_id;
            $param_KitID = $kit_type;
            $param_Qty = $kit_qty;
            $param_Price = $kit_price;
            $param_State = $party_state;
            $param_Date = $party_date . " " . $party_time;
            $param_City = $party_city;
            $param_StreetAddr = $party_address;
            if ($stmt->execute()) {
                header("location: BookingView.php");
            } else $err_msg = "OOPS! Something Went Wrong. Could add booking. " . $stmt->error;
            $stmt->close();
        } else $err_msg = "OOPS! Something Went Wrong. ";
    }
    $mysqli->close();
} elseif (isset($_GET["BookID"]) && !empty(trim($_GET["BookID"]))) { // Check existence of id parameter before processing further
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
    <title>MakerFun Dashboard</title>
</head>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="text-center">
        <h1 class="h4 text-gray-900 mb-4">Modify Booking</h1>
    </div>
    <! /////////////////////////////////////////////////////////////////// >
    <! // Use special character replacement to guard against SQL injection >
    <! // Set the form action to have SUBMIT call this page again by POST  >
    <! // with the values entered into the form sent along with the call   >
    <! /////////////////////////////////////////////////////////////////// >
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="user" method="post">
        <div class="form-group <?php echo (!empty($err_msg)) ? 'text-danger' : ''; ?>">
            <span class="help-block "><?php echo $err_msg; ?></span>
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
                <input type="date" name="partyDate" class="form-control form-control-user" id="partyDate"
                       placeholder="Date of Party" value="<?php echo $party_date; ?>">
            </div>
            <div class="col-sm-6">
                <label for="partyTime">Time (HH:MM AM/PM):</label>
                <input type="time" name="partyTime" class="form-control form-control-user" id="partyTime"
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
                <a class="btn btn-secondary btn-user btn-block" href="UglyBookingView.php">Cancel</a>
            </div>
        </div>
    </form>


</div>
<!-- End of Main Content -->


</html>