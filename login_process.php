<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $query = "SELECT EMAIL, PASSWORD, FULL_NAME, USER_ROLE, PROFILE_PIC FROM SYSTEM.AQUIL_ACCOUNTS WHERE UPPER(EMAIL) = UPPER(:email)";
    $stid = oci_parse($conn, $query);
    
    oci_bind_by_name($stid, ":email", $email);
    oci_execute($stid);
    $row = oci_fetch_array($stid, OCI_ASSOC);
    if ($is_valid) {
        if (isset($_POST['remember'])) {
            setcookie("user_login", $email, time() + (86400 * 30), "/"); 
        } else {
            if (isset($_COOKIE["user_login"])) {
                setcookie("user_login", "", time() - 3600, "/");
            }
        }
        if ($_SESSION['user_role'] === 'admin') {
            header("Location: Admin/admin_dashboard.php");
        } else {
            header("Location: index.php?login=success");
        }
        exit();
    }
    if ($row) {
        $db_password = trim($row['PASSWORD']);
        $is_valid = false;
        if (password_verify($password, $db_password) || $password === $db_password) {
            $is_valid = true;
        }
        if ($is_valid) {
            $_SESSION['user_email'] = $row['EMAIL'];
            $_SESSION['user_name'] = $row['FULL_NAME'];
            $_SESSION['user_role'] = trim($row['USER_ROLE']); 
            $_SESSION['full_name'] = $row['FULL_NAME']; // THIS IS KEY for the WhatsApp message
            $_SESSION['profile_pic'] = !empty($row['PROFILE_PIC']) ? $row['PROFILE_PIC'] : 'product_img/default_user.jpg';
            $dbPath = $row['PROFILE_PIC'];
            if (!empty($dbPath) && file_exists($dbPath)) {
                $_SESSION['profile_pic'] = $dbPath;
            } else {
                $_SESSION['profile_pic'] = 'product_img/default_user.jpg';
            }
            if ($_SESSION['user_role'] === 'admin') {
                header("Location: Admin/admin_dashboard.php");
            } else {
                header("Location: index.php?login=success");
            }
            exit();
        } else {
            header("Location: login.php?error=invalid_credentials");
            exit();
        }
    } else {
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
} // This was the missing closing brace for the POST check
?>