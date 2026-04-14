<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require '../db.php'; 
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

$total_inquiries = 0;
$inquiry_query = "SELECT COUNT(*) AS TOTAL FROM SYSTEM.AQUIL_CONTACT_MSG";
$stid_inq = oci_parse($conn, $inquiry_query);

if (@oci_execute($stid_inq)) {
    $inquiry_row = oci_fetch_array($stid_inq, OCI_ASSOC);
    $total_inquiries = $inquiry_row['TOTAL'] ?? 0;
}

$total_orders = 0;
$order_query = "SELECT COUNT(*) AS TOTAL FROM SYSTEM.AQUIL_ORDERS";
$stid_ord = oci_parse($conn, $order_query);
if (@oci_execute($stid_ord)) {
    $order_row = oci_fetch_array($stid_ord, OCI_ASSOC);
    $total_orders = $order_row['TOTAL'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquil Life | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
    <meta http-equiv="refresh" content="300">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h4 class="mb-0">Aquil<span style="color:var(--aquil-green)">Life</span></h4>
        <small style="opacity: 0.6; letter-spacing: 1px;">ADMIN PANEL</small>
    </div>
<div class="nav-links">
    <a href="admin_dashboard.php" class="active"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="admin_inbox.php"><i class="fas fa-envelope"></i> Contact Inbox</a>
    <a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Customer Orders</a>
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
        <h5 class="mb-0">Welcome back, <strong>Admin !!</strong></h5>
        <span class="badge badge-success px-3 py-2" style="border-radius: 8px;">
            <i class="fas fa-shield-alt mr-1"></i> Administrator
        </span>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="stat-card shadow">
                <div class="icon-box bg-light text-primary"><i class="fas fa-comments"></i></div>
                <h6 class="text-muted small text-uppercase font-weight-bold">Total Inquiries</h6>
                <h2 class="font-weight-bold"><?php echo $total_inquiries; ?></h2>
                <a href="admin_inbox.php" class="action-link text-primary mt-2">
                    View Inbox <i class="fas fa-arrow-right ml-2" style="font-size: 0.8rem;"></i>
                </a>
            </div>
        </div>

    <div class="col-md-4">
        <div class="stat-card shadow">
            <div class="icon-box bg-light text-success">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h6 class="text-muted small text-uppercase font-weight-bold">Customer Orders</h6>
            <h2 class="font-weight-bold"><?php echo $total_orders; ?></h2>
            <a href="admin_orders.php" class="action-link text-success mt-2">
                Manage Orders <i class="fas fa-arrow-right ml-2" style="font-size: 0.8rem;"></i>
            </a>
        </div>
    </div>

        <div class="col-md-4">
            <div class="stat-card" style="border-bottom: 4px solid var(--aquil-green);">
                <div class="icon-box bg-light text-warning"><i class="fas fa-server"></i></div>
                <h6 class="text-muted small text-uppercase font-weight-bold">System Status</h6>
                <h2 class="text-success font-weight-bold">Healthy</h2>
                <p class="small text-muted mb-0">
                    <i class="fas fa-database mr-1"></i> Oracle DB Connected
                </p>
            </div>
        </div>
    </div>

    <div class="quick-actions-card text-center">
        <h5 class="font-weight-bold" style="color: var(--aquil-navy);">Quick Actions</h5>
        <p class="text-muted mb-4">Use the sidebar to manage your website content efficiently.</p>
    </div>
</div>

</body>
</html>