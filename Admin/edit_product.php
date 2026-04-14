<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require '../db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: manage_products.php"); exit(); }

// 1. Fetch Current Data (Including Description)
$query = "SELECT * FROM SYSTEM.AQUIL_PRODUCTS WHERE PRODUCT_ID = :id";
$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":id", $id);
oci_execute($stid);
$product = oci_fetch_array($stid, OCI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $_POST['name'];
    $cat   = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $img   = $_POST['image_url']; 
    $desc  = $_POST['description']; 
    
    // SQL uses :descr to avoid reserved word conflicts
    $update = "UPDATE SYSTEM.AQUIL_PRODUCTS 
               SET NAME = :n, CATEGORY = :c, PRICE = :p, STOCK = :s, IMAGE_URL = :img, DESCRIPTION = :descr 
               WHERE PRODUCT_ID = :pid";
    
    $up_stid = oci_parse($conn, $update);
    
    oci_bind_by_name($up_stid, ":n", $name);
    oci_bind_by_name($up_stid, ":c", $cat);
    oci_bind_by_name($up_stid, ":p", $price);
    oci_bind_by_name($up_stid, ":s", $stock);
    oci_bind_by_name($up_stid, ":img", $img);
    oci_bind_by_name($up_stid, ":descr", $desc); 
    oci_bind_by_name($up_stid, ":pid", $id);
    
    if (oci_execute($up_stid)) {
        oci_commit($conn);
        header("Location: manage_products.php?msg=updated");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | Aquil Life</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --aquil-navy: #0a3d62; --aquil-green: #2a7f62; --sidebar-width: 260px; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        .sidebar { width: var(--sidebar-width); height: 100vh; background: var(--aquil-navy); position: fixed; }
        .sidebar-header { padding: 20px; text-align: center; background: rgba(0,0,0,0.1); color: white; }
        .nav-links a { display: block; padding: 15px 25px; color: rgba(255,255,255,0.7); text-decoration: none; border-left: 4px solid transparent; }
        .nav-links a:hover, .nav-links a.active { color: white; background: rgba(255,255,255,0.1); border-left-color: var(--aquil-green); }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; min-height: 100vh; }
        .top-bar { background: white; padding: 15px 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-label { font-weight: 700; color: #555; font-size: 0.85rem; text-uppercase; }
        .form-control { border-radius: 8px; border: 1px solid #e0e0e0; padding: 12px; transition: 0.3s; }
        .form-control:focus { border-color: var(--aquil-green); box-shadow: 0 0 0 0.2rem rgba(42, 127, 98, 0.1); }
        .preview-box { background: #fff; border-radius: 15px; padding: 20px; border: 1px solid #eee; text-align: center; }
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
        <a href="admin_inbox.php" ><i class="fas fa-envelope"></i> Contact Inbox</a>
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
    <div class="top-bar d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Commodity: <span class="text-primary"><?php echo htmlspecialchars($product['NAME']); ?></span></h5>
        <span class="badge badge-primary px-3 py-2">Product ID: #<?php echo $id; ?></span>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-5">
            <form method="POST">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group mb-4">
                            <label class="form-label">PRODUCT NAME</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['NAME']); ?>" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">CATEGORY</label>
                            <select name="category" class="form-control">
                                <option value="DAILY WELLNESS" <?php if($product['CATEGORY'] == 'DAILY WELLNESS') echo 'selected'; ?>>Daily Wellness</option>
                                <option value="MATERNAL HEALTH" <?php if($product['CATEGORY'] == 'MATERNAL HEALTH') echo 'selected'; ?>>Maternal Health</option>
                                <option value="LITTLE CHAMPS" <?php if($product['CATEGORY'] == 'LITTLE CHAMPS') echo 'selected'; ?>>Little Champs</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">PRODUCT DESCRIPTION</label>
                            <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product['DESCRIPTION'] ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">PRICE (₹)</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['PRICE']; ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">STOCK LEVEL</label>
                                <input type="number" name="stock" class="form-control" value="<?php echo $product['STOCK']; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5 border-left pl-md-5">
                        <label class="form-label">PRODUCT PREVIEW</label>
                        <div class="preview-box mb-4">
                            <img id="preview" src="../<?php echo $product['IMAGE_URL']; ?>" class="img-fluid" style="max-height: 250px; border-radius: 10px;" onerror="this.src='../product_img/default.webp'">
                        </div>
                        <div class="form-group">
                            <label class="form-label">IMAGE PATH</label>
                            <input type="text" name="image_url" id="image_input" class="form-control" value="<?php echo htmlspecialchars($product['IMAGE_URL']); ?>">
                        </div>
                    </div>
                </div>

                <hr class="my-5">
                <div class="d-flex justify-content-between">
                    
                    <a href="manage_products.php" class="btn btn-secondary px-4"><i class="fa-solid fa-arrow-left mr-2"></i> Back to List</a>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm"><i class="fa-solid fa-cloud-arrow-up mr-2"></i>Update Information</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('image_input').addEventListener('input', function() {
        document.getElementById('preview').src = '../' + this.value;
    });
</script>
</body>
</html>