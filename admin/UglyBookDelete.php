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

// Create the database connection object
require_once "DB_Conn.php";

// Process delete operation after confirmation
if(isset($_POST["BookID"]) && !empty($_POST["BookID"])){

    // Prepare a delete statement
    $sql = "DELETE FROM BOOKING WHERE BookingID = ?";

    if($stmt = $mysqli->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $param_bookid);

        // Set parameters
        $param_bookid = trim($_POST["BookID"]);

        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // Records deleted successfully. Redirect to landing page
            header("location: BookingView.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    $stmt->close();

    // Close connection
    $mysqli->close();
} else{
    // Check existence of id parameter
    if(empty(trim($_GET["BookID"]))){
        // URL doesn't contain id parameter. Redirect to error page
        header("location: BookingView.php");
        exit();
    }
    $book_id = trim($_GET["BookID"]);

    $sql = "SELECT * FROM BOOKING WHERE BookingID = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $param_bookid);
        $param_bookid = $book_id;
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
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

        // Close connection
        $mysqli->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Delete Booking</title>
</head>

                                    <! /////////////////////////////////////////////////////////////////// >
                                    <! // Use special character replacement to guard against SQL injection >
                                    <! // Set the form action to have SUBMIT call this page again by POST  >
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="user" method="post">

                                        <div class="form-group row">
                                            <div class="col-sm-6 col-med-3 col-lg-5 col-xl-5">
                                                <input type="submit" class="btn btn-google btn-user btn-block" value="Confirm Delete">
                                            </div>
                                            <div class="col-sm-6 col-med-3 col-lg-5 col-xl-5">
                                                <a class="btn btn-secondary btn-user btn-block" href="UglyBookingView.php">Cancel</a>
                                            </div>
                                        </div>
                                        <input name="BookID" type="hidden" value="<?php echo $book_id; ?>">

                                    </form>

                                    <div class="form-group">
                                        <div class="col-sm-12 col-med-12 col-lg-12">
                                            <label>Customer ID:</label>
                                            <span class="form-control-static text-danger" ><b><?php echo $customer_id; ?></b></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12 col-med-12 col-lg-12">
                                            <label>Kit Type:</label>
                                            <span class="form-control-static text-danger" ><b><?php echo $kit_type; ?></b></span>
                                        </div>
                                        <div class="col-sm-12 col-med-12 col-lg-12">
                                            <label>Kit Qty:</label>
                                            <span class="form-control-static text-danger" ><b><?php echo $kit_qty; ?></b></span>
                                        </div>
                                        <div class="col-sm-12 col-med-12 col-lg-12">
                                            <label for="KitPrice">Kit Price:</label>
                                            <span class="form-control-static text-danger" ><b><?php echo $kit_price; ?></b></span>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="col-sm-12 col-med-12 col-lg-12">
                                            <label>Date of Party:</label>
                                            <span class="form-control-static text-danger" ><b><?php echo $party_date; ?></b></span>
                                        </div>
                                        <div class="col-sm-12 col-med-12 col-lg-12">
                                            <label>Time:</label>
                                            <span class="form-control-static text-danger" ><b><?php echo $party_time; ?></b></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12 col-med-12 col-lg-12">
                                            <label>Address:</label>
                                            <span class="form-control-static text-danger" ><b><?php echo $party_address; ?></b></span>
                                        </div>
                                        <div class="col-sm-12 col-med-12 col-lg-12">
                                            <label>City:</label>
                                            <span class="form-control-static text-danger" ><b><?php echo $party_city; ?></b></span>
                                        </div>
                                        <div class="col-sm-12 col-med-12 col-lg-12">
                                            <label>State:</label>
                                            <span class="form-control-static text-danger" ><b><?php echo $party_state; ?></b></span>
                                        </div>
                                    </div>

</body>

</html>