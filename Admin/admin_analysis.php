<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require '../db.php'; 
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquil Life | Business Analysis</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
    <meta http-equiv="refresh" content="300">
    <style>
        .dashboard-container {
            background: #f8f9fa url('https://i.gifer.com/ZZ5H.gif') center no-repeat;
            background-size: 50px;
            border-radius: 15px;
            padding: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #eee;
            overflow: hidden;
            height: 85vh; /* Makes the dashboard take up most of the screen */
        }
        iframe {
            border: none;
            width: 100%;
            height: 100%;
            border-radius: 10px;
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
        <a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Customer Orders</a>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
        <a href="manage_products.php"><i class="fas fa-pills"></i> Products</a>
          <a href="admin_health.php" ><i class="fas fa-heartbeat"></i> Customer Health</a>
         <a href="admin_analysis.php" class="active"><i class="fas fa-chart-line"></i> Business Analysis</a>
        <div class="dropdown-divider border-secondary mx-3"></div>
        <a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
<div class="main-content">
    <div class="top-bar d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Strategic <strong>Analysis</strong></h5>
            <p class="text-muted small mb-0">Live Power BI Data Visualization</p>
        </div>
        <span class="badge badge-success px-3 py-2" style="border-radius: 8px;">
            <i class="fas fa-sync-alt mr-1"></i> Live Data
        </span>
    </div>

    <div class="dashboard-container">
        <iframe 
            title="AquilLife Analysis" 
            src="https://app.powerbi.com/reportEmbed?reportId=f649fcb2-3304-44e1-a9b6-c5a6c24a62f7&autoAuth=true&ctid=0231311b-bd53-498f-a024-8f7210ce2de6&navContentPaneEnabled=false" 
            allowFullScreen="true">
        </iframe>
    </div>
</div>
</body>
</html>