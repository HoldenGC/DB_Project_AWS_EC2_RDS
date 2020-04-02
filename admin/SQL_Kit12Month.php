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
