<?php
require 'db.php'; 
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $otp = rand(1000, 9999); 
    $query = "SELECT EMAIL FROM SYSTEM.AQUIL_ACCOUNTS WHERE UPPER(EMAIL) = UPPER(:email)";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":email", $email);
    oci_execute($stid);
    
    if (oci_fetch_array($stid, OCI_ASSOC)) {
        $update = "UPDATE SYSTEM.AQUIL_ACCOUNTS SET RECOVERY_CODE = :otp WHERE UPPER(EMAIL) = UPPER(:email)";
        $upStid = oci_parse($conn, $update);
        oci_bind_by_name($upStid, ":otp", $otp);
        oci_bind_by_name($upStid, ":email", $email);
        oci_execute($upStid);

        echo json_encode([
            'status' => 'success', 
            'otp' => $otp, 
            'message' => 'User found.'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'That email is not in our database.']);
    }
    oci_free_statement($stid);
    oci_close($conn);
}
?>