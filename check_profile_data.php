<?php
error_reporting(0); 
header('Content-Type: application/json');

session_start();
require 'db.php'; 

$response = ['profile_complete' => false];

if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];
    $query = "SELECT MOBILE, LOCATION FROM SYSTEM.AQUIL_ACCOUNTS WHERE EMAIL = :email";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ':email', $email);
    
    if (oci_execute($stid)) {
        $row = oci_fetch_array($stid, OCI_ASSOC);
        if ($row && !empty($row['MOBILE']) && !empty($row['LOCATION'])) {
            $response['profile_complete'] = true;
        }
    }
    oci_free_statement($stid);
}

echo json_encode($response);
exit;
?>