<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require '../db.php';
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}
$query = "SELECT 
            oi.ORDER_ID, 
            a.FULL_NAME AS CUSTOMER_NAME, 
            LISTAGG(oi.PRODUCT_NAME, ', ') WITHIN GROUP (ORDER BY oi.PRODUCT_NAME) AS COMMODITY, 
            SUM(oi.QUANTITY) AS TOTAL_QTY, 
            SUM(oi.QUANTITY * oi.PRICE) AS TOTAL_SALES, 
            MAX(o.ORDER_DATE) AS ORDER_DATE 
          FROM SYSTEM.AQUIL_ORDER_ITEMS oi
          JOIN SYSTEM.AQUIL_ORDERS o ON oi.ORDER_ID = o.ORDER_ID
          JOIN SYSTEM.AQUIL_ACCOUNTS a ON o.USER_EMAIL = a.EMAIL
          GROUP BY oi.ORDER_ID, a.FULL_NAME
          ORDER BY ORDER_DATE DESC";

$stid = oci_parse($conn, $query);
oci_execute($stid);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquil Life | Sales Ledger</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
 <style>
.table-responsive {
    overflow-x: hidden; /* Removes the bottom scrollbar */
    padding: 10px;
}

.table {
    width: 100% !important;
    table-layout: fixed; /* This forces columns to stay within the width */
    font-size: 0.85rem; /* Slightly smaller font to save space */
}
.table th, .table td {
    padding: 10px 5px !important; /* Reduced padding */
    vertical-align: middle;
    word-wrap: break-word; /* Allows long text to wrap inside the cell */
}

/* 3. Set custom widths for important columns */
.col-id { width: 8%; }
.col-date { width: 15%; }
.col-name { width: 20%; }
.col-item { width: 22%; }
.col-qty { width: 8%; text-align: center; }
.col-price { width: 12%; }
.col-total { width: 15%; font-weight: 700; }

/* 4. Prevent the "Date" and "Price" from splitting into two lines */
.nowrap {
    white-space: nowrap;
}
.table thead { 
    background: var(--aquil-navy); 
    color: white; 
}
 </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h4 class="mb-0">Aquil<span style="color:var(--aquil-green)">Life</span></h4>
        <small style="opacity: 0.6; letter-spacing: 1px;">ADMIN PANEL</small>
    </div>
    <div class="nav-links">
        <a href="admin_dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="admin_inbox.php"><i class="fas fa-envelope"></i> Contact Inbox</a>
        <a href="admin_orders.php" class="active"><i class="fas fa-file-invoice-dollar"></i> Customer Orders</a>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
        <a href="manage_products.php"><i class="fas fa-pills"></i> Products</a>
        <a href="admin_health.php" ><i class="fas fa-heartbeat"></i> Customer Health</a>
         <a href="admin_analysis.php"><i class="fas fa-chart-line"></i> Business Analysis</a>
        <div class="dropdown-divider border-secondary mx-3"></div>
        <a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="top-bar">
        <h5 class="mb-0"><i class="fas fa-shopping-cart text-primary mr-2"></i>Customer Orders</h5>
        <span class="badge badge-success px-3 py-2">Administrator</span>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="inbox-card"> <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="pl-4 col-id">ID</th> 
                                <th class="col-name">Customer</th>
                                <th class="col-item">Products</th> 
                                <th class="text-center col-qty">Qty</th>
                                <th class="text-center col-total">Grand Total</th>
                                <th class="col-date">Order Date</th>
                                <th class="pr-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = oci_fetch_array($stid, OCI_ASSOC)): ?>
                            <tr>
                                <td class="pl-4 font-weight-bold text-primary">#<?php echo $row['ORDER_ID']; ?></td>
                                <td class="text-dark font-weight-600"><?php echo htmlspecialchars($row['CUSTOMER_NAME'] ?? 'Guest'); ?></td>
                                <td class="text-muted small"><?php echo htmlspecialchars($row['COMMODITY']); ?></td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-light border" style="font-size: 0.8rem; px-3;">
                                        <?php echo $row['TOTAL_QTY']; ?>
                                    </span>
                                </td>
                                <td class="text-center font-weight-bold text-success" style="font-size: 0.95rem;">
                                    ₹<?php echo number_format($row['TOTAL_SALES'], 2); ?>
                                </td>
                                <td class="small text-muted nowrap"><?php echo $row['ORDER_DATE']; ?></td>
                                <td class="pr-4 text-right">
                                    <button type="button" class="btn btn-sm btn-info view-receipt shadow-sm" data-id="<?php echo $row['ORDER_ID']; ?>">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-xl" role="document" >
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="receiptContent">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Fetching receipt details...</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
            </div>
        </div>
    </div>
</div>
</body>

<<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    $('.view-receipt').on('click', function() {
        var orderId = $(this).data('id');
        
        // Open modal
        $('#receiptModal').modal('show');
        
        // Load Content
        $.ajax({
            url: '../receipt.php',
            type: 'GET',
            data: { order_id: orderId, modal: 1 },
            success: function(response) {
                $('#receiptContent').html(response);
            }
        });
    });
});
// Replace the window.print() in your modal footer with this:
function printReceiptOnly() {
    var content = document.getElementById('receiptContent').innerHTML;
    var printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Receipt</title>');
    // Add your receipt CSS link here
    printWindow.document.write('<link rel="stylesheet" href="../css/receipt.css">'); 
    printWindow.document.write('</head><body>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>
</html>
