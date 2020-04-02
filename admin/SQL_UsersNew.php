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

$sql = "SELECT CustId, FName, LName, Email, Passwd, CreatedAt 
FROM USERS 
WHERE datediff(now(), CreatedAt) < 15";

if ($newUsrs = $mysqli->query($sql)) {
    if ($newUsrs->num_rows > 0) {

        echo "<table class=\"table table-bordered\" id=\"NewUserTable\" width=\"100%\" cellspacing=\"0\">";
        echo "<thead>";
        echo "<tr>";
        echo "<th>First Name</th>";
        echo "<th>Last Name</th>";
        echo "<th>Email</th>";
	    echo "<th>Password</th>";
        echo "<th>Date Created</th>";
        echo "</tr>";
        echo "</thead>";

        echo "<tfoot>";
        echo "<tr>";
        echo "<th>First Name</th>";
        echo "<th>Last Name</th>";
        echo "<th>Email</th>";
	    echo "<th>Password</th>";
        echo "<th>Date Created</th>";
        echo "</tr>";
        echo "</tfoot>";
        echo "<tbody>";

        while ($row = $newUsrs->fetch_array()) {
            echo "<tr>";
            echo "<td>" . $row['FName'] . "</td>";
            echo "<td>" . $row['LName'] . "</td>";
            echo "<td>" . $row['Email'] . "</td>";
            echo "<td>" . $row['Passwd'] . "</td>";
            echo "<td>" . $row['CreatedAt'] . "</td>";
            echo "</tr>";

        }
        echo "</tbody>";
        echo "</table>";
        $newUsrs->free();

    }
}

?>
