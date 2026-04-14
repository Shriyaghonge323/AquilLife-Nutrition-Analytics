<?php
header('Content-Type: application/json');
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

$userEmail = $_SESSION['user_email'];
$generatedId = null;
$totalOrderAmount = 0; // Initialize total
$isDirectBuy = isset($_POST['product_id']) && !empty($_POST['product_id']);

// 1. Prepare Items & Calculate Total First
$items = [];
if ($isDirectBuy) {
    $sqlP = "SELECT NAME, PRICE FROM SYSTEM.AQUIL_PRODUCTS WHERE PRODUCT_ID = :pid";
    $stidP = oci_parse($conn, $sqlP);
    oci_bind_by_name($stidP, ":pid", $_POST['product_id']);
    oci_execute($stidP);
    $p = oci_fetch_array($stidP, OCI_ASSOC);
    
    $qty = (int)$_POST['qty'];
    $price = (float)$p['PRICE'];
    $totalOrderAmount = $qty * $price; // Calculation
    $items[] = ['name' => $p['NAME'], 'qty' => $qty, 'price' => $price];
} else {
    foreach ($_SESSION['cart'] as $c) {
        $lineTotal = (int)$c['qty'] * (float)$c['price'];
        $totalOrderAmount += $lineTotal; // Accumulate total
        $items[] = ['name' => $c['name'], 'qty' => $c['qty'], 'price' => $c['price']];
    }
}

// 2. Insert Main Order with TOTAL_AMOUNT
// Note: Ensure TOTAL_AMOUNT is a column in your SYSTEM.AQUIL_ORDERS table
$sqlOrder = "INSERT INTO SYSTEM.AQUIL_ORDERS (USER_EMAIL, ORDER_DATE, TOTAL_AMOUNT, STATUS) 
             VALUES (:email, CURRENT_TIMESTAMP, :total, 'Confirmed') 
             RETURNING ORDER_ID INTO :oid";

$stidOrder = oci_parse($conn, $sqlOrder);
oci_bind_by_name($stidOrder, ":email", $userEmail);
oci_bind_by_name($stidOrder, ":total", $totalOrderAmount); // Bind the calculated total
oci_bind_by_name($stidOrder, ":oid", $generatedId, -1, SQLT_INT);

if (oci_execute($stidOrder)) {
    // Insert Items loop...
    foreach ($items as $item) {
        $sqlI = "INSERT INTO SYSTEM.AQUIL_ORDER_ITEMS (ORDER_ID, PRODUCT_NAME, QUANTITY, PRICE) 
                 VALUES (:oid, :n, :q, :p)";
        $stidI = oci_parse($conn, $sqlI);
        oci_bind_by_name($stidI, ":oid", $generatedId);
        oci_bind_by_name($stidI, ":n", $item['name']);
        oci_bind_by_name($stidI, ":q", $item['qty']);
        oci_bind_by_name($stidI, ":p", $item['price']);
        oci_execute($stidI);
    }
    unset($_SESSION['cart']);
    if (ob_get_length()) ob_clean();
    echo json_encode(['status' => 'success', 'order_id' => $generatedId]);
    exit; 
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database execution failed']);
    exit;
}