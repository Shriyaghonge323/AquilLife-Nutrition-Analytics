<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Capture ALL form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $otp = rand(100000, 999999); 

    // --- TASK 1: SAVE THE MESSAGE TO THE DB ---
    // This ensures your contact messages actually show up in the table from your screenshot
    $msgQuery = "INSERT INTO SYSTEM.AQUIL_CONTACT_MSG (FULL_NAME, EMAIL, SUBJECT, MESSAGE) 
                 VALUES (:name, :email, :subject, :message)";
    $msgStid = oci_parse($conn, $msgQuery);
    oci_bind_by_name($msgStid, ":name", $name);
    oci_bind_by_name($msgStid, ":email", $email);
    oci_bind_by_name($msgStid, ":subject", $subject);
    oci_bind_by_name($msgStid, ":message", $message);
    
    $msgSaved = oci_execute($msgStid, OCI_NO_AUTO_COMMIT); // Don't commit yet

    // --- TASK 2: PASSWORD RECOVERY LOGIC ---
    // Check if user exists and update recovery code
    $checkQuery = "SELECT FULL_NAME FROM SYSTEM.AQUIL_ACCOUNTS WHERE UPPER(EMAIL) = UPPER(:email)";
    $checkStid = oci_parse($conn, $checkQuery);
    oci_bind_by_name($checkStid, ":email", $email);
    oci_execute($checkStid);
    
    if ($row = oci_fetch_array($checkStid, OCI_ASSOC)) {
        // User exists, update their recovery code
        $update = "UPDATE SYSTEM.AQUIL_ACCOUNTS SET RECOVERY_CODE = :otp WHERE UPPER(EMAIL) = UPPER(:email)";
        $upStid = oci_parse($conn, $update);
        oci_bind_by_name($upStid, ":otp", $otp);
        oci_bind_by_name($upStid, ":email", $email);
        oci_execute($upStid, OCI_NO_AUTO_COMMIT);
    }

    // --- FINAL STEP: COMMIT EVERYTHING ---
    if ($msgSaved) {
        oci_commit($conn);
        header("Location: contact.php?status=sent&name=" . urlencode($name));
    } else {
        oci_rollback($conn);
        header("Location: contact.php?status=error");
    }

    oci_close($conn);
    exit();
}
?>