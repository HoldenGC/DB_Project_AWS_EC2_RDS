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

$sql = "SELECT CustId, FName, LName, Email, Passwd, CreatedAt FROM USERS ORDER BY CreatedAt desc";

if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {

        echo "<table class=\"table table-bordered\" id=\"dataTable\" width=\"100%\" cellspacing=\"0\">";
        echo "<thead>";
        echo "<tr>";
        echo "<th>ID Number</th>";
        echo "<th>First Name</th>";
        echo "<th>Last Name</th>";
        echo "<th>Email</th>";
        echo "<th>Date Created</th>";
        echo "</tr>";
        echo "</thead>";

        echo "<tfoot>";
        echo "<tr>";
        echo "<th>ID Number</th>";
        echo "<th>First Name</th>";
        echo "<th>Last Name</th>";
        echo "<th>Email</th>";
        echo "<th>Date Created</th>";
        echo "</tr>";
        echo "</tfoot>";
        echo "<tbody>";

        while ($row = $result->fetch_array()) {
            echo "<tr>";
            echo "<td>" . $row['CustId'] . "</td>";
            echo "<td>" . $row['FName'] . "</td>";
            echo "<td>" . $row['LName'] . "</td>";
            echo "<td>" . $row['Email'] . "</td>";
            echo "<td>" . $row['CreatedAt'] . "</td>";
            echo "</tr>";

        }
        echo "</tbody>";
        echo "</table>";
        $result->free();

    }
}

?>
