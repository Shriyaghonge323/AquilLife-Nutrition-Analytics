<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require '../db.php';

// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $_POST['name'];
    $cat   = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $img   = $_POST['image_url'] ?? 'product_img/default.webp'; 
    $desc  = $_POST['description']; 

    // INSERT includes the DESCRIPTION column
    $sql = "INSERT INTO SYSTEM.AQUIL_PRODUCTS (NAME, CATEGORY, PRICE, STOCK, IMAGE_URL, DESCRIPTION) 
            VALUES (:n, :c, :p, :s, :img, :descr)";
    
    $stid = oci_parse($conn, $sql);
    
    oci_bind_by_name($stid, ":n", $name);
    oci_bind_by_name($stid, ":c", $cat);
    oci_bind_by_name($stid, ":p", $price);
    oci_bind_by_name($stid, ":s", $stock);
    oci_bind_by_name($stid, ":img", $img);
    oci_bind_by_name($stid, ":descr", $desc); 
    
    if (oci_execute($stid)) {
        oci_commit($conn);
        header("Location: manage_products.php?msg=added");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Product | Aquil Life</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --aquil-navy: #0a3d62; --aquil-green: #2a7f62; --sidebar-width: 260px; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: var(--sidebar-width); height: 100vh; background: var(--aquil-navy); position: fixed; }
        .sidebar-header { padding: 20px; text-align: center; background: rgba(0,0,0,0.1); color: white; }
        .nav-links a { display: block; padding: 15px 25px; color: rgba(255,255,255,0.7); text-decoration: none; border-left: 4px solid transparent; }
        .nav-links a:hover, .nav-links a.active { color: white; background: rgba(255,255,255,0.1); border-left-color: var(--aquil-green); }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; }
        .card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h4>Aquil<span style="color:var(--aquil-green)">Life</span></h4>
        <small>ADMIN PANEL</small>
    </div>
<div class="nav-links">
    <a href="admin_dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
    <a href="admin_inbox.php"><i class="fas fa-envelope"></i> Contact Inbox</a>
    <a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Customer Orders</a>
    <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
    <a href="manage_products.php" class="active"><i class="fas fa-pills"></i> Products</a>
    <a href="admin_health.php" ><i class="fas fa-heartbeat"></i> Customer Health</a>
     <a href="admin_analysis.php"><i class="fas fa-chart-line"></i> Business Analysis</a>
    <div class="dropdown-divider border-secondary mx-3"></div>
    <a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 font-weight-bold text-navy">Add New Product to Inventory</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($error_msg)): ?>
                            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                                <label class="font-weight-bold">Product Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Nutriplus Junior" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Product Description</label>
                                <textarea name="description" class="form-control" rows="5" placeholder="Enter detailed product benefits..."><?php echo htmlspecialchars($product['DESCRIPTION'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="font-weight-bold">Category</label>
                                    <select name="category" class="form-control">
                                        <option value="DAILY WELLNESS">Daily Wellness</option>
                                        <option value="MATERNAL HEALTH">Maternal Health</option>
                                        <option value="LITTLE CHAMPS">Little Champs</option>
                                        <option value="SPECIALIZED CARE">Specialized Care</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold">Price (₹)</label>
                                    <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="font-weight-bold">Stock</label>
                                    <input type="number" name="stock" class="form-control" placeholder="0" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Image URL (Optional)</label>
                                <input type="text" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="manage_products.php" class="btn btn-secondary px-4">Cancel</a>
                                <button type="submit" class="btn btn-success px-5 shadow-sm">Save Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>