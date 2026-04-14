<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

$email = $_SESSION['user_email'] ?? null;
if (!$email) {
    header("Location: login.php");
    exit;
}

$user_query = "SELECT FULL_NAME, MOBILE, LOCATION, PROFILE_PIC FROM SYSTEM.AQUIL_ACCOUNTS WHERE EMAIL = :email";
$user_stid = oci_parse($conn, $user_query);
oci_bind_by_name($user_stid, ":email", $email);

if (oci_execute($user_stid)) {
    $user_data = oci_fetch_array($user_stid, OCI_ASSOC);
}

$user_name = $user_data['FULL_NAME'] ?? 'User';
$user_email = $email;
$_SESSION['user_mobile'] = $user_data['MOBILE'] ?? 'Not Set';
$_SESSION['user_location'] = $user_data['LOCATION'] ?? 'Not Set';
$_SESSION['profile_pic'] = $user_data['PROFILE_PIC'] ?? '/aquil/product_img/default_user.jpg';

$query = "SELECT ORDER_ID, TOTAL_AMOUNT, TO_CHAR(ORDER_DATE, 'DD Mon YYYY') as ODATE, STATUS 
          FROM SYSTEM.AQUIL_ORDERS WHERE USER_EMAIL = :email ORDER BY ORDER_DATE DESC";
$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":email", $email);
oci_execute($stid); 

include 'header.php'; 
include 'nav.php'; 
?>
<style>
    :root {
        --aquil-navy: #0A3D62; /* Matches Admin Sidebar */
        --aquil-teal: #2a7f62; /* Matches Logo Healthcare Green */
        --bg-light: #f4f7f6;
    }

    body { background-color: var(--bg-light); }

    /* Unified Header */
    .dashboard-hero {
        background: linear-gradient(135deg, var(--aquil-navy) 0%, #003366 100%);
        padding: 100px 0 80px;
        color: white;
        text-align: center;
        border-radius: 0 0 50px 50px;
    }

    /* Floating Profile Card */
    .profile-card {
        background: white;
        border-radius: 25px;
        padding: 30px;
        margin-top: -60px; /* Pulls card up into the hero section */
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: none;
    }

    .profile-img {
        width: 120px;
        height: 120px;
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-top: -80px; /* Overlaps the hero background */
    }

    /* Modernized Purchase History Table */
    .history-card {
        background: white;
        border-radius: 25px;
        padding: 30px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }

    .order-row {
        background: #fff;
        border: 1px solid #edf2f7;
        border-radius: 15px;
        margin-bottom: 12px;
        transition: 0.3s;
    }

    .order-row:hover {
        border-color: var(--aquil-teal);
        transform: translateX(5px);
    }

    .status-confirmed {
        background: rgba(42, 127, 98, 0.1);
        color: var(--aquil-teal);
        font-weight: 600;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 13px;
    }

    .price-tag {
        color: var(--aquil-teal);
        font-weight: 700;
        font-size: 1.1rem;
    }
</style>

<div class="dashboard-hero">
    <div class="container" data-aos="fade-down">
        <h1 class="font-weight-bold">My Health Center</h1>
        <p class="opacity-75">Manage your profile and track your nutritional journey.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <div class="col-lg-4 mb-4" data-aos="fade-up">
            <div class="profile-card text-center">
            <?php 
            $pic = $_SESSION['profile_pic'];
            if (!file_exists($pic) || empty($pic)) {
                $pic = 'product_img/default_user.jpg';
            }
            ?>
            <img src="<?php echo htmlspecialchars($pic); ?>" class="rounded-circle profile-img mb-3">                
            <h4 class="font-weight-bold mb-1"><?php echo htmlspecialchars($user_name); ?></h4>
                <p class="text-muted small mb-4"><?php echo htmlspecialchars($user_email); ?></p>
                
                <div class="text-left p-3 mb-4 rounded" style="background: #f8f9fa;">
                    <small class="text-muted d-block mb-1"><i class="fas fa-map-marker-alt mr-2"></i>Location</small>
                    <p class="small mb-3"><?php echo $_SESSION['user_location']; ?></p>
                    <small class="text-muted d-block mb-1"><i class="fas fa-phone mr-2"></i>Contact</small>
                    <p class="small mb-0"><?php echo $_SESSION['user_mobile']; ?></p>
                </div>
                
                <a href="update_profile.php" class="btn btn-block btn-dark rounded-pill py-2 font-weight-bold">
                    <i class="fas fa-user-edit mr-2"></i> Update Profile
                </a>
            </div>
        </div>

        <div class="col-lg-8 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="history-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="font-weight-bold m-0">Purchase History</h4>
                    <a href="commodities.php" class="text-primary font-weight-bold small">New Order +</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = oci_fetch_array($stid, OCI_ASSOC)): ?>
                            <tr class="order-row shadow-sm">
                                <td class="font-weight-bold">#AQ-<?php echo $order['ORDER_ID']; ?></td>
                                <td><span class="status-confirmed"><i class="fas fa-check-circle mr-1"></i> <?php echo $order['STATUS']; ?></span></td>
                                <td class="text-muted small"><?php echo $order['ODATE']; ?></td>
                                <td class="price-tag">
    ₹<?php echo number_format($order['TOTAL_AMOUNT'] ?? 0, 2); ?>
</td>
                                <td class="text-right">
                                    <a href="receipt.php?order_id=<?php echo $order['ORDER_ID']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Details</a>
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

<?php include 'footer.php'; ?>