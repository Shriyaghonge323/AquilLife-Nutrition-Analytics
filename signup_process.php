<?php
require 'db.php'; 
header('Content-Type: application/json'); // Tell the browser we are sending JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['phone']);
    $location = trim($_POST['location']); 
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user'; 

    $query = "INSERT INTO SYSTEM.AQUIL_ACCOUNTS (EMAIL, PASSWORD, USER_ROLE, FULL_NAME, MOBILE, LOCATION) 
              VALUES (:email, :pw, :role, :fname, :mobile, :loc)";
    
    $stid = oci_parse($conn, $query);

    oci_bind_by_name($stid, ":email", $email);
    oci_bind_by_name($stid, ":pw", $password);
    oci_bind_by_name($stid, ":role", $role);
    oci_bind_by_name($stid, ":fname", $fullname);
    oci_bind_by_name($stid, ":mobile", $mobile);
    oci_bind_by_name($stid, ":loc", $location);

    $result = @oci_execute($stid);

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        $e = oci_error($stid);
        $message = "Registration Error";
        
        if (strpos($e['message'], 'ORA-00001') !== false) {
            $message = "This email or phone number is already registered!";
        }
        
        echo json_encode(['status' => 'error', 'message' => $message]);
    }
    
    oci_free_statement($stid);
    oci_close($conn);
}
?>