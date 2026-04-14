
<?php
$username = "SYSTEM"; 
$password = "Manager123"; 
$connection_string = "localhost:1521/orcl"; 

$conn = oci_connect($username, $password, $connection_string);

if (!$conn) {
    $e = oci_error();
    die("Database Connection failed: " . htmlspecialchars($e['message'])); 
}
?>