<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require '../db.php';

// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

// Handle Delete Request
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $del_query = "DELETE FROM SYSTEM.AQUIL_PRODUCTS WHERE PRODUCT_ID = :id";
    $del_stid = oci_parse($conn, $del_query);
    oci_bind_by_name($del_stid, ":id", $id);
    if (oci_execute($del_stid)) {
        header("Location: manage_products.php?msg=deleted");
        exit();
    }
}

// Fetch all products (Including DESCRIPTION)
$query = "SELECT PRODUCT_ID, NAME, CATEGORY, PRICE, STOCK, DESCRIPTION FROM SYSTEM.AQUIL_PRODUCTS ORDER BY PRODUCT_ID DESC";
$stid = oci_parse($conn, $query);
oci_execute($stid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products | Aquil Life</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --aquil-navy: #0a3d62; --aquil-green: #2a7f62; --sidebar-width: 260px; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: var(--sidebar-width); height: 100vh; background: var(--aquil-navy); position: fixed; }
        .sidebar-header { padding: 20px; text-align: center; background: rgba(0,0,0,0.1); color: white; }
        .nav-links a { display: block; padding: 15px 25px; color: rgba(255,255,255,0.7); text-decoration: none; border-left: 4px solid transparent; }
        .nav-links a.active { color: white; background: rgba(255,255,255,0.1); border-left-color: var(--aquil-green); }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; }
        .top-bar { background: white; padding: 15px 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; }
    </style>
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
    <a href="manage_users.php" ><i class="fas fa-users"></i> Manage Users</a>
    <a href="manage_products.php" class="active"><i class="fas fa-pills"></i> Products</a>
    <a href="admin_health.php" ><i class="fas fa-heartbeat"></i> Customer Health</a>
     <a href="admin_analysis.php"><i class="fas fa-chart-line"></i> Business Analysis</a>
    <div class="dropdown-divider border-secondary mx-3"></div>
    <a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
</div>

<div class="main-content">
    <div class="top-bar d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-navy font-weight-bold">Inventory Management</h5>
        <a href="add_product.php" class="btn btn-success btn-sm px-4 shadow-sm"><i class="fas fa-plus mr-2"></i> Add New Product</a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success shadow-sm">Action completed successfully!</div>
    <?php endif; ?>

    <div class="card shadow-sm border-0" style="border-radius:15px; overflow:hidden;">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Description Preview</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = oci_fetch_array($stid, OCI_ASSOC)): ?>
                <tr>
                    <td>#<?php echo $row['PRODUCT_ID']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['NAME']); ?></strong></td>
                    <td><span class="badge badge-info"><?php echo htmlspecialchars($row['CATEGORY']); ?></span></td>
                    <td><small class="text-muted"><?php echo substr(htmlspecialchars($row['DESCRIPTION'] ?? ''), 0, 50); ?>...</small></td>
                    <td class="font-weight-bold">₹<?php echo number_format($row['PRICE'], 2); ?></td>
                    <td><?php echo $row['STOCK']; ?></td>
                    <td class="text-center">
                        <a href="edit_product.php?id=<?php echo $row['PRODUCT_ID']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <a href="manage_products.php?delete_id=<?php echo $row['PRODUCT_ID']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>