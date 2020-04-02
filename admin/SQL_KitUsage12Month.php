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

$sql = "SELECT 
    DATE_FORMAT(PartyDate,\"%M %Y\") AS Month, 
    KitID AS Kit,
    SUM(B.QtyKits) AS Qty
FROM BOOKING AS B 
WHERE (DATEDIFF(NOW(), B.PartyDate) < 365) AND  (DATEDIFF(NOW(), B.PartyDate) > 0 )
GROUP BY Month, KitID
ORDER BY KitID ASC, PartyDate DESC;";

if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
        for ($i = 0; $i <= 12; $i++) {
            $row = $result->fetch_array();
            $Month[$i] = $row["Month"];
            $Kit1[$i] = $row["Kit"];
        }
        for ($i = 0; $i <= 12; $i++) {
            $row = $result->fetch_array();
            $Kit2[$i] = $row["Kit"];
        }
        for ($i = 0; $i <= 12; $i++) {
            $row = $result->fetch_array();
            $Kit3[$i] = $row["Kit"];
        }
        $result->free();

    }
}

?>
