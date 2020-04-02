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

$sql = "SELECT ROUND(SUM(B.QtyKits*B.PriceEa),2) AS Future
FROM BOOKING as B 
WHERE datediff(now(), B.PartyDate) < 0;";

if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array()) {
            echo $row["Future"];
        }

        $result->free();

    }
}

?>
