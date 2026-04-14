<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Security & Connection
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}
require '../db.php';

// 2. Optimized Delete Logic
if (isset($_GET['delete_email']) && isset($_GET['delete_time'])) {
    $email = $_GET['delete_email'];
    $time = $_GET['delete_time'];

    $del_query = "DELETE FROM SYSTEM.AQUIL_CONTACT_MSG 
                WHERE EMAIL = :email 
                AND TO_CHAR(SUBMITTED_AT, 'DD-MON-YY HH:MI AM') = :sub_time";
    
    $del_stid = oci_parse($conn, $del_query);
    oci_bind_by_name($del_stid, ":email", $email);
    oci_bind_by_name($del_stid, ":sub_time", $time);

    if (oci_execute($del_stid)) {
        oci_commit($conn);
        header("Location: admin_inbox.php?status=deleted");
        exit();
    } else {
        header("Location: admin_inbox.php?status=error");
        exit();
    }
}

// 3. Fetch Data
$query = "SELECT FULL_NAME, EMAIL, SUBJECT, MESSAGE, 
          TO_CHAR(SUBMITTED_AT, 'DD-MON-YY HH:MI AM') as SUB_TIME 
          FROM SYSTEM.AQUIL_CONTACT_MSG 
          WHERE EMAIL IS NOT NULL 
          ORDER BY SUBMITTED_AT DESC";

$stid = oci_parse($conn, $query);
oci_execute($stid);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aquil Life | Admin Inbox</title>
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
        <a href="admin_inbox.php" class="active"><i class="fas fa-envelope"></i> Contact Inbox</a>
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
        <h5 class="mb-0"><i class="fas fa-envelope text-success mr-2"></i>Customer Inquiries</h5>
        <span class="badge badge-success px-3 py-2">Administrator</span>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <div class="alert-inbox <?php echo ($_GET['status'] === 'deleted' ? 'alert-deleted' : 'alert-error'); ?>" id="statusAlert">
            <i class="fas <?php echo ($_GET['status'] === 'deleted' ? 'fa-check-circle' : 'fa-exclamation-circle'); ?>"></i>
            <span><?php echo ($_GET['status'] === 'deleted' ? 'The inquiry has been successfully removed.' : 'An error occurred.'); ?></span>
        </div>
        <script>
            setTimeout(() => {
                const alert = document.getElementById('statusAlert');
                if (alert) { alert.style.display = 'none'; }
            }, 4000);
        </script>
    <?php endif; ?>

    <div class="inbox-card">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = 0;
                while ($row = oci_fetch_array($stid, OCI_ASSOC)): 
                    $count++;
                    $msg = (is_object($row['MESSAGE'])) ? $row['MESSAGE']->load() : ($row['MESSAGE'] ?? 'No message');-
                    $randomPhone = "91" . rand(7000000000, 9999999999); 
                    $waText = "Hello " . htmlspecialchars($row['FULL_NAME']) . "! This is the Admin from Aquil Life. I am reaching out regarding your inquiry: " . htmlspecialchars($row['SUBJECT']);
                    $waUrl = "https://wa.me/" . $randomPhone . "?text=" . urlencode($waText);
                    $mailSub = "Response from Aquil Life: " . ($row['SUBJECT'] ?? 'Inquiry');
                    $mailBody = "Dear " . htmlspecialchars($row['FULL_NAME']) . ",%0D%0A%0D%0A" .
                                "Thank you for contacting Aquil Life regarding your message:%0D%0A" .
                                "\"" . htmlspecialchars($msg) . "\"%0D%0A%0D%0A" .
                                "RESPONSE: [Type your message here]%0D%0A%0D%0ABest Regards,%0D%0AAquil Life Administration";
                    $gmailUrl = "https://mail.google.com/mail/?view=cm&fs=1" .
                                "&to=" . urlencode($row['EMAIL']) . 
                                "&su=" . rawurlencode($mailSub) . 
                                "&body=" . $mailBody;
                ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($row['FULL_NAME']); ?></strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars($row['EMAIL']); ?></small>
                    </td>
                    <td>
                        <span class="badge badge-success mb-1"><?php echo htmlspecialchars($row['SUBJECT']); ?></span>
                        <div class="small text-secondary"><?php echo nl2br(htmlspecialchars($msg)); ?></div>
                    </td>
                    <td class="small text-muted"><?php echo $row['SUB_TIME']; ?></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="<?php echo $waUrl; ?>" target="_blank" class="btn btn-sm btn-outline-success" title="WhatsApp Reply">
                                <i class="fab fa-whatsapp"></i>
                            </a>

                            <a href="<?php echo $gmailUrl; ?>" target="_blank" class="btn btn-sm btn-gmail-blue " title="Gmail Draft">
                                <i class="fas fa-envelope"></i>
                            </a>

                            <a href="admin_inbox.php?delete_email=<?php echo urlencode($row['EMAIL']); ?>&delete_time=<?php echo urlencode($row['SUB_TIME']); ?>" 
                               class="btn btn-sm btn-outline-danger" onclick="return confirm('Permanently delete this inquiry?');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if ($count == 0): ?>
                    <tr><td colspan="4" class="text-center py-5">No valid inquiries found in the database.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>