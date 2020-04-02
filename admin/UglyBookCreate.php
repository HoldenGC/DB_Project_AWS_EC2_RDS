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


// The FIRST PASS skips this code because the first time through, this page was not called by POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {  // Check to see if arrived here from the form

    // Create the database connection object
    require_once "DB_Conn.php";

    if (empty(trim($_POST["CustID"]))) {                            // Check for empty customer ID in POST
        $err_msg = "Please enter a customer ID";                    // Give error message if empty
    } else {
        $customer_id = trim($_POST["CustID"]);                      // Trim whitespace on ends
        $sql = "SELECT Email FROM USERS WHERE CustID = ?";          // Store SQL to lookup email based on CustID
        if ($stmt = $mysqli->prepare($sql)) {                       // Prepare SQL for binding to stop SQL injection
            $stmt->bind_param("s", $param_custid);    // Bind the CustID parameter into the SQL statement
            $param_custid = $customer_id;                           // Set the CustID parameter
            if ($stmt->execute()) {                                 // Execute the SQL Statement
                $stmt->store_result();                              // Store the resulting email
                if ($stmt->num_rows < 1) {                          // Make sure an email returned
                    $err_msg = "Customer ID not found. Please enter a valid one.";
                }
            } else $err_msg = "OOPS! Something Went Wrong.";
        }
        $stmt->close();                                             // Destroys the statement object
    }

    if (empty(trim($_POST["KitType"]))) {                           // Check each field of the form for empty
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

    if (empty($err_msg)) {                                           // Execute if all fields had values
        // Build the SQL to insert the new booking into the database using parameter binding to avoid SQL injection
        $sql = "INSERT INTO BOOKING (BookingID, CustomerId, KitID, QtyKits, PriceEa, PartyState, PartyDate, 
                    PartyCity, PartyStreetAddr) values (0,?,?,?,?,?,?,?,?)";
        if ($stmt = $mysqli->prepare($sql)) {                       // Initialize the connection with the SQL
            $stmt->bind_param("iiidssss", $param_CustID, $param_KitID,
                $param_Qty, $param_Price, $param_State, $param_Date, $param_City, $param_StreetAddr);
            $param_CustID = $customer_id;
            $param_KitID = $kit_type;
            $param_Qty = $kit_qty;                                  // Bind all the user input fields in a Statement
            $param_Price = $kit_price;                              //   to avoid SQL injection from user input
            $param_State = $party_state;
            $param_Date = $party_date . " " . $party_time;
            $param_City = $party_city;
            $param_StreetAddr = $party_address;
            if ($stmt->execute()) {                                 // Insert the new booking into the database
                header("location: BookingView.php");
            } else $err_msg = "OOPS! Something Went Wrong. Could add booking. " . $stmt->error;
            $stmt->close();                                         // Destroy the statement object
        } else $err_msg = "OOPS! Something Went Wrong.";
    }
    $mysqli->close();                                               // Destroy the connection object
    // This is where the 'FIRST PASS skip' ends
} else $customer_id = $_SESSION["CustId"];

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create Booking</title>
</head>
<div class="text-center">
    <h1 class="h4 text-gray-900 mb-4">Create Booking</h1>
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
    <div class="form-group row">
        <div class="col-sm-6 mb-3 mb-sm-0">
            <! // Create a button that says "Add Booking" and submits form when clicked >
            <input type="submit" class="btn btn-primary btn-user btn-block" value="Add Booking">
        </div>
        <div class="col-sm-6">
            <a class="btn btn-secondary btn-user btn-block" href="BookingView.php">Cancel</a>
        </div>
    </div>
</form>

</html>