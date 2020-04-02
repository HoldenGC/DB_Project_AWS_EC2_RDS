<?php
//
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

// Build table of bookings
// Create the database connection object
require_once "DB_Conn.php";

// SQL to get all the bookings and show fields including calculated party revenue for each
$sql = "SELECT 
    B.BookingID,                                    
    U.CustId, U.FName, U.LName, U.Email,        
    B.PartyDate, B.KitID,                    
    ROUND(B.PriceEa, 2) as 'Kit_Price',       
    B.QtyKits,                                  
    ROUND(B.QtyKits*B.PriceEa,2) as 'Party_Rev',  
    B.PartyStreetAddr, B.PartyCity, B.PartyState 
FROM BOOKING `B` INNER JOIN USERS U 
ON `B`.CustomerID = U.CustId;";                     // Join tables to get customer name

// Check the mysqli connection to make sure it completed successfully and is not empty
if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
        // Echo out the HTML to build the table Header and Footer
        echo "<table class=\"table table-bordered\" id=\"bookingTable\" width=\"100%\" cellspacing=\"0\">";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Action</th>";
        echo "<th>Booking ID</th>";
        echo "<th>First Name</th>";
        echo "<th>Last Name</th>";
        echo "<th>Email</th>";
	    echo "<th>Party Date</th>";
        echo "<th>Kit Type</th>";
        echo "<th>Kit Price</th>";
        echo "<th>Quantity</th>";
        echo "<th>Total Price</th>";

        echo "</tr>";
        echo "</thead>";

        echo "<tfoot>";
        echo "<tr>";
        echo "<th>Action</th>";
        echo "<th>Booking ID</th>";
        echo "<th>First Name</th>";
        echo "<th>Last Name</th>";
        echo "<th>Email</th>";
        echo "<th>Party Date</th>";
        echo "<th>Kit Type</th>";
        echo "<th>Kit Price</th>";
        echo "<th>Quantity</th>";
        echo "<th>Total Price</th>";

        echo "</tr>";
        echo "</tfoot>";
        echo "<tbody>";
        // Echo out the HTML to build each row of the results returned from the database
        while ($row = $result->fetch_array()) {
            echo "<tr>";
            echo "<td>";
            // Print the Icons for VIEW, EDIT, and DELETE
            echo "<a href='BookRead.php?BookID=" . $row['BookingID'] . "' title='View Record' data-toggle='tooltip'><span class='fas fa-eye'>&nbsp&nbsp</span></a>";
            echo "<a href='BookUpdate.php?BookID=" . $row['BookingID'] . "' title='Update Record' data-toggle='tooltip'><span class='fas fa-pencil-alt'></i>&nbsp&nbsp</span></a>";
            echo "<a href='BookDelete.php?BookID=" . $row['BookingID'] . "' title='Delete Record' data-toggle='tooltip'><span class='fas fa-trash-alt'>&nbsp&nbsp</span></a>";
            echo "</td>";
            echo "<td>" . $row['BookingID'] . "</td>";
            echo "<td>" . $row['FName'] . "</td>";
            echo "<td>" . $row['LName'] . "</td>";
            echo "<td>" . $row['Email'] . "</td>";
            echo "<td>" . $row['PartyDate'] . "</td>";
            echo "<td>" . $row['KitID'] . "</td>";
            echo "<td>" . $row['Kit_Price'] . "</td>";
            echo "<td>" . $row['QtyKits'] . "</td>";
            echo "<td>" . $row['Party_Rev'] . "</td>";

            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        $result->free();

    }
}

?>
