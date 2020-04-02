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

if ($newUsrs = $mysqli->query($sql)) {
    if ($newUsrs->num_rows > 0) {

        echo "<table class=\"table table-bordered\" id=\"NewUserTable\" width=\"100%\" cellspacing=\"0\">";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Month</th>";
        echo "<th>Kit Type</th>";
        echo "<th>Kit Revenue</th>";
	    echo "<th>Quantity</th>";
        echo "</tr>";
        echo "</thead>";

        echo "<tfoot>";
        echo "<tr>";
        echo "<tr>";
        echo "<th>Month</th>";
        echo "<th>Kit Type</th>";
        echo "<th>Kit Revenue</th>";
        echo "<th>Quantity</th>";
        echo "</tr>";
        echo "</tfoot>";
        echo "<tbody>";

        while ($row = $newUsrs->fetch_array()) {
            echo "<tr>";
            echo "<td>" . $row['Month'] . "</td>";
            echo "<td>" . $row['Kit'] . "</td>";
            echo "<td>" . $row['KitRev'] . "</td>";
            echo "<td>" . $row['Qty'] . "</td>";
            echo "</tr>";

        }
        echo "</tbody>";
        echo "</table>";
        $newUsrs->free();

    }
}

?>
