<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require '../db.php';

// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

// 1. Handle Delete Action
if (isset($_GET['delete_email'])) {
    $email = $_GET['delete_email'];
    $del_query = "DELETE FROM SYSTEM.AQUIL_ACCOUNTS WHERE EMAIL = :email";
    $del_stid = oci_parse($conn, $del_query);
    oci_bind_by_name($del_stid, ":email", $email);
    
    if (oci_execute($del_stid)) {
        oci_commit($conn);
        header("Location: manage_users.php?status=deleted");
        exit();
    }
}

// 2. Fetch Users - Filter out rows with NULL FULL_NAME
$query = "SELECT FULL_NAME, EMAIL, USER_ROLE, TO_CHAR(CREATED_AT, 'DD-MON-YYYY') as JOIN_DATE 
          FROM SYSTEM.AQUIL_ACCOUNTS 
          WHERE FULL_NAME IS NOT NULL 
          ORDER BY CREATED_AT DESC";

$stid = oci_parse($conn, $query);
oci_execute($stid);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aquil Life | User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h4 class="mb-0">Aquil<span style="color:var(--aquil-green)">Life</span></h4>
        <small style="opacity: 0.6; letter-spacing: 1px;">ADMIN PANEL</small>
    </div>
<div class="nav-links">
    <a href="admin_dashboard.php" ><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="admin_inbox.php"><i class="fas fa-envelope"></i> Contact Inbox</a>
    <a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Customer Orders</a>
    <a href="manage_users.php" class="active"><i class="fas fa-users"></i> Manage Users</a>
    <a href="manage_products.php"><i class="fas fa-pills"></i> Products</a>
    <a href="admin_health.php" ><i class="fas fa-heartbeat"></i> Customer Health</a>
     <a href="admin_analysis.php"><i class="fas fa-chart-line"></i> Business Analysis</a>
    <div class="dropdown-divider border-secondary mx-3"></div>
    <a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
</div>

<div class="main-content">
    <h3 class="font-weight-bold mb-4">User Directory</h3>

    <div class="user-card">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="px-4">Full Name</th>
                    <th class="px-4">Email Address</th>
                    <th class="px-4">Role</th>
                    <th class="px-4">Joined Date</th>
                    <th class="px-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = oci_fetch_array($stid, OCI_ASSOC)): ?>
                <tr>
                    <td class="px-4 py-4 font-weight-bold"><?php echo htmlspecialchars($row['FULL_NAME']); ?></td>
                    <td class="px-4 py-4"><?php echo htmlspecialchars($row['EMAIL']); ?></td>
                    <td class="px-4 py-4">
                        <span class="badge badge-pill <?php echo (strtolower($row['USER_ROLE']) == 'admin') ? 'badge-primary' : 'badge-primary'; ?> px-3">
                        <?php echo strtoupper($row['USER_ROLE']); ?>
                    </span>
                    </td>
                    <td class="px-4 py-4 text-muted small"><?php echo $row['JOIN_DATE']; ?></td>
                    <td class="px-4 py-4 text-center">
                        <a href="manage_users.php?delete_email=<?php echo urlencode($row['EMAIL']); ?>" 
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Permanently delete this user?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>