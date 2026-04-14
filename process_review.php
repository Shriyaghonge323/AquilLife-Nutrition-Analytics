<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];
    $name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : explode('@', $email)[0]; 
    $rating = intval($_POST['rating']);
    $comment_data = $_POST['comment'];
    $query = "INSERT INTO SYSTEM.AQUIL_REVIEWS (USER_EMAIL, USER_NAME, RATING, COMMENT_TEXT) 
              VALUES (:email, :name, :rating, EMPTY_CLOB()) 
              RETURNING COMMENT_TEXT INTO :rev_text";
    $stid = oci_parse($conn, $query);
    $clob = oci_new_descriptor($conn, OCI_D_LOB);

    oci_bind_by_name($stid, ":email", $email);
    oci_bind_by_name($stid, ":name", $name);
    oci_bind_by_name($stid, ":rating", $rating);
    oci_bind_by_name($stid, ":rev_text", $clob, -1, OCI_B_CLOB);

    if (oci_execute($stid, OCI_NO_AUTO_COMMIT)) {
        $clob->save($comment_data);
        oci_commit($conn);
        header("Location: index.php?review=success");
        exit(); 
    } else {
        $e = oci_error($stid);
        echo "Error: " . htmlspecialchars($e['message']);
    }
    
    $clob->free();
    oci_free_statement($stid);
    oci_close($conn);
} else {
    header("Location: index.php");
    exit();
}
?>