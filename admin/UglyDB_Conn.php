<?php
// Set variables needed to establish connection with Database (remove extra spaces)
define('DB_SERVER'  , 'URL/IP Address of your Database server goes here!    ');
define('DB_USERNAME', 'User account name being used on DB server goes here! ');
define('DB_PASSWORD', 'Password for the DB user account goes here!          ');
define('DB_NAME'    , 'Name of Database goes here!                          ');

// Create mysqli connection object to connect to the database
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verify connection was successful, store error otherwise
if($mysqli === false){
    die("ERROR: Connection to Database failed. " . $mysqli->connect_error);
}
?>