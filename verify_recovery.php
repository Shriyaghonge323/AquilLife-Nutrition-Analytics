<?php
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $otp = trim($_POST['otp']);
    $new_pw = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $query = "SELECT RECOVERY_CODE FROM SYSTEM.AQUIL_ACCOUNTS WHERE EMAIL = :email";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":email", $email);
    oci_execute($stid);
    $row = oci_fetch_array($stid, OCI_ASSOC);

    if ($row && $row['RECOVERY_CODE'] == $otp) {
        $update = "UPDATE SYSTEM.AQUIL_ACCOUNTS SET PASSWORD = :pw, RECOVERY_CODE = NULL WHERE EMAIL = :email";
        $upStid = oci_parse($conn, $update);
        oci_bind_by_name($upStid, ":pw", $new_pw);
        oci_bind_by_name($upStid, ":email", $email);
        
        if (oci_execute($upStid)) {
            echo json_encode(['status' => 'success', 'message' => 'Password has been updated!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid recovery code. Please try again.']);
    }
    oci_free_statement($stid);
    oci_close($conn);
}
?>