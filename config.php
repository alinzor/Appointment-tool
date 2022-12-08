<?php

/// mysql hostname
$hostname = 'localhost';

/// mysql username
$username = 'root';

/// mysql password
$password = '';

/// mysql database
$databasename = 'consultants';

$conn = mysqli_connect($hostname, $username, $password);
if (!$conn)
{
    die('Could not connect to database: ' . mysqli_connect_error());
}
mysqli_select_db($conn, $databasename);

?>