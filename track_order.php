<?php
require 'db.php';

$orderID = $_GET['id'] ?? null;

if (!$orderID) {
    die("Invalid Tracking Link.");
}

// Clean the ID (remove 'AQ-' prefix if it was added to the QR data)
$cleanID = str_replace('AQ-', '', $orderID);

$query = "SELECT o.ORDER_ID, o.ORDER_DATE, o.USER_EMAIL, a.FULL_NAME 
          FROM SYSTEM.AQUIL_ORDERS o
          JOIN SYSTEM.AQUIL_ACCOUNTS a ON o.USER_EMAIL = a.EMAIL
          WHERE o.ORDER_ID = :oid";

$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":oid", $cleanID);
oci_execute($stid);
$order = oci_fetch_array($stid, OCI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Verification | Aquil Life</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .verify-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 90%; }
        .status-icon { color: #2a7f62; font-size: 4rem; margin-bottom: 20px; }
        .order-id { background: #0a3d62; color: white; padding: 5px 15px; border-radius: 50px; font-size: 0.9rem; display: inline-block; margin-bottom: 15px; }
        h2 { color: #333; margin: 10px 0; }
        p { color: #666; font-size: 0.95rem; line-height: 1.5; }
        .btn-home { display: inline-block; margin-top: 25px; background: #2a7f62; color: white; text-decoration: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; transition: 0.3s; }
        .btn-home:hover { background: #0a3d62; }
    </style>
</head>
<body>

<div class="verify-card">
    <?php if ($order): ?>
        <div class="status-icon"><i class="fas fa-check-circle"></i></div>
        <div class="order-id">ID: #AQ-<?php echo $order['ORDER_ID']; ?></div>
        <h2>Authentic Order</h2>
        <p>This order was placed by <strong><?php echo htmlspecialchars($order['FULL_NAME']); ?></strong> on <strong><?php echo $order['ORDER_DATE']; ?></strong>.</p>
        <p>Current Status: <span style="color: #2a7f62; font-weight: bold;">Processing for Delivery</span></p>
    <?php else: ?>
        <div class="status-icon" style="color: #e74c3c;"><i class="fas fa-times-circle"></i></div>
        <h2>Invalid Order</h2>
        <p>We couldn't find an order with this ID in our system. Please contact support.</p>
    <?php endif; ?>
    
    <a href="index.php" class="btn-home">Return to Home</a>
</div>

</body>
</html>