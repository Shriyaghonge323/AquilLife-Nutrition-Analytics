<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php'; 

$orderID = $_GET['id'] ?? $_GET['order_id'] ?? null;
$isModal = isset($_GET['modal']) && $_GET['modal'] == 1;

if (!$orderID) {
    echo "Order ID missing.";
    exit;
}
$query = "SELECT a.FULL_NAME, a.EMAIL, a.MOBILE, a.LOCATION, o.ORDER_DATE 
          FROM SYSTEM.AQUIL_ORDERS o
          JOIN SYSTEM.AQUIL_ACCOUNTS a ON o.USER_EMAIL = a.EMAIL
          WHERE o.ORDER_ID = :oid";

$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":oid", $orderID);
oci_execute($stid);
$orderInfo = oci_fetch_array($stid, OCI_ASSOC);

$itemsToPrint = [];
$itemQuery = "SELECT PRODUCT_NAME, QUANTITY, PRICE 
              FROM SYSTEM.AQUIL_ORDER_ITEMS
              WHERE ORDER_ID = :oid";

$stidItems = oci_parse($conn, $itemQuery);
oci_bind_by_name($stidItems, ":oid", $orderID);
oci_execute($stidItems);

while ($row = oci_fetch_array($stidItems, OCI_ASSOC)) {
    $itemsToPrint[] = $row;
}

$subtotal = 0;
$computer_ip = "192.168.0.106"; 
$tracking_url = "http://" . $computer_ip . "/Aquil/track_order.php?id=" . $orderID;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #2a7f62; --secondary: #0a3d62; }
        body { font-family: 'Segoe UI', sans-serif; color: #333; background-color:#0a3d62; margin: 0; padding: 20px; background: #f9f9f9; }
        .back-link {
            text-decoration: none;
            color: var(--secondary);
            font-size: 0.85rem;
            font-weight: 700;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
            color: var(--primary);
            transform: translateX(-3px);
        }

        .print-btn {
            background: #f1f5f9;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            color: var(--secondary);
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .print-btn:hover {
            background: #e2e8f0;
        }

        /* Hide navigation when printing */
        @media print {
            .back-link, .print-btn { display: none; }
            body { background: white; padding: 0; }
            .receipt-container { box-shadow: none; border: none; }
        }
        .receipt-container { 
            max-width: 800px; 
            margin: auto; 
            background: white; 
            padding: 40px; 
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .header-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo-section h2 { margin: 0; color: var(--secondary); font-weight: 800; display: flex; align-items: center; gap: 15px; }
        .logo-section h2 span { color: var(--primary); }
        
        .order-title { 
            background: var(--secondary); 
            color: white; 
            padding: 8px 16px; 
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 30px; }
        .info-box h6 { color: var(--primary); margin: 0 0 8px 0; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-box p { margin: 3px 0; font-size: 0.9rem; line-height: 1.4; }

        .receipt-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
.receipt-table th { 
    background: #f8fbfa; /* Very light teal to match your brand */
    color: #2a7f62; 
    text-transform: uppercase; 
    letter-spacing: 0.5px;
  }        .receipt-table td { padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; }

        .footer-flex { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 30px;}
        .qr-code-wrapper { text-align: center; border: 1px solid #eee; padding: 10px; border-radius: 6px; background: #fff; }
        .total-wrapper { width: 260px; text-align: right;}
        .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.9rem; }
        .grand-total { border-top: 2px solid var(--secondary); margin-top: 10px; padding-top: 10px; font-weight: 800; font-size: 1.2rem; color: var(--secondary); }
        <?php if ($isModal): ?>
            body { background: white; padding: 0; }
            .receipt-container { box-shadow: none; padding: 20px; border: none; }
        <?php endif; ?>
    </style>
</head>
<body>

<div class="receipt-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="services.php" class="back-link">
            <i class="fas fa-chevron-left"></i> Back to Shop
        </a>
        <button onclick="window.print();" class="print-btn">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>

    <div class="header-main">
        <div class="logo-section">
            <img src="product_img/no_tag_logo.png" 
                 alt="Logo" 
                 style="height: 50px; width: auto; display: block;"
                 onerror="this.src='/Aquil/product_img/no_tag_logo.png';"> 
            <p style="margin: 5px 0 0 0; font-size: 0.7rem; color: #888; font-weight: 600; letter-spacing: 0.5px;">PROFESSIONAL NUTRITIONAL CARE</p>
        </div>
        <div class="order-title">Official Receipt</div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h6><i class="fas fa-file-invoice"></i> Order Details</h6>
            <p><strong>Invoice No:</strong> #AQ-<?php echo $orderID; ?></p>
            <p><strong>Order Date:</strong> <?php echo $orderInfo['ORDER_DATE'] ?? date('d-M-Y'); ?></p>
        </div>
        <div class="info-box">
            <h6><i class="fas fa-user-check"></i> Billed To</h6>
            <p><strong><?php echo htmlspecialchars($orderInfo['FULL_NAME'] ?? 'Customer'); ?></strong></p>
            <p><i class="fas fa-envelope mr-1" style="font-size:0.7rem;"></i> <?php echo htmlspecialchars($orderInfo['EMAIL'] ?? ''); ?></p>
            
            <?php if (!empty($orderInfo['MOBILE'])): ?>
                <p><i class="fas fa-phone mr-1" style="font-size:0.7rem;"></i> <?php echo htmlspecialchars($orderInfo['MOBILE']); ?></p>
            <?php endif; ?>

            <?php if (!empty($orderInfo['LOCATION'])): ?>
                <p><i class="fas fa-map-marker-alt mr-1" style="font-size:0.7rem;"></i> <?php echo htmlspecialchars($orderInfo['LOCATION']); ?></p>
            <?php else: ?>
                <div style="background: #fff5f5; border: 1px solid #feb2b2; padding: 5px; border-radius: 4px; margin-top: 5px;">
                    <a href="update_profile.php" style="color: #c53030; font-size: 0.7rem; text-decoration: none; font-weight: bold;">
                        <i class="fas fa-exclamation-triangle"></i> Add address for delivery
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <table class="receipt-table">
        <thead>
            <tr>
                <th>Product Description</th>
                <th>Qty</th>
                <th style="text-align: right;">Unit Price</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itemsToPrint as $item): 
                $lineTotal = $item['QUANTITY'] * $item['PRICE'];
                $subtotal += $lineTotal;
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($item['PRODUCT_NAME']); ?></strong></td>
                <td><?php echo $item['QUANTITY']; ?></td>
                <td style="text-align: right;">₹<?php echo number_format($item['PRICE'], 2); ?></td>
                <td style="text-align: right; font-weight: 600;">₹<?php echo number_format($lineTotal, 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer-flex">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div class="qr-code-wrapper">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=85x85&data=<?php echo urlencode($tracking_url); ?>" alt="QR">
        </div>
        <div>
            <div style="color: var(--secondary); font-weight: 800; font-size: 0.8rem; letter-spacing: 1px;">
                <i class="fas fa-shield-alt"></i> VERIFIED RECEIPT
            </div>
            <div style="font-size: 0.65rem; color: #666; margin-top: 4px;">
                Generated: <?php echo date('d-M-Y H:i:s'); ?>
            </div>
        </div>
    </div>

    <div class="total-wrapper">
        <div class="total-row">
            <span>Subtotal</span>
            <span>₹<?php echo number_format($subtotal, 2); ?></span>
        </div>
        <div class="total-row">
            <span>GST (0%)</span>
            <span>₹0.00</span>
        </div>
        <div class="total-row grand-total">
            <span>Grand Total</span>
            <span>₹<?php echo number_format($subtotal, 2); ?></span>
        </div>
    </div>
</div>
</div>

</body>
</html>