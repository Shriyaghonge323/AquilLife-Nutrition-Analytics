<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}

// Fetch Health Data
$query = "SELECT * FROM SYSTEM.VW_CUSTOMER_HEALTH ORDER BY TOTAL_SPENT DESC";
$stid = oci_parse($conn, $query);
oci_execute($stid);

// Summary Counts
$counts = ['Champion' => 0, 'Healthy' => 0, 'At-Risk' => 0, 'Needs Attention' => 0];
$data = [];
while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
    $counts[$row['HEALTH_STATUS']]++;
    $data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquil Life | Customer Health</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        :root {
            --health-champion: #0f766e;
            --health-healthy: #4338ca;
            --health-atrisk: #b45309;
            --health-attention: #be123c;
            
            --bg-champion: #f0fdfa;
            --bg-healthy: #eef2ff;
            --bg-atrisk: #fffbeb;
            --bg-attention: #fff1f2;
        }

        /* Layout & Responsiveness */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }

        @media (max-width: 992px) {
            .sidebar { width: 70px; overflow: hidden; }
            .sidebar h4, .sidebar span, .sidebar .nav-links a span { display: none; }
            .main-content { margin-left: 70px; }
        }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 15px; }
            .sidebar { display: none; }
            .health-card h2 { font-size: 1.5rem; }
        }

        /* Cards & Badges */
        .health-card { border: none; border-radius: 12px; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .health-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        
        .status-badge { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.7rem; text-transform: uppercase; display: inline-block; white-space: nowrap; }
        
        .bg-champion { background: var(--bg-champion); color: var(--health-champion); border: 1px solid #ccfbf1; }
        .bg-healthy { background: var(--bg-healthy); color: var(--health-healthy); border: 1px solid #e0e7ff; }
        .bg-atrisk { background: var(--bg-atrisk); color: var(--health-atrisk); border: 1px solid #fef3c7; }
        .bg-needsattention { background: var(--bg-attention); color: var(--health-attention); border: 1px solid #ffe4e6; }

        .card-champion { background: linear-gradient(135deg, #14b8a6, #0f766e) !important; }
        .card-healthy { background: linear-gradient(135deg, #6366f1, #4338ca) !important; }
        .card-atrisk { background: linear-gradient(135deg, #f59e0b, #b45309) !important; }
        .card-needsattention { background: linear-gradient(135deg, #f43f5e, #be123c) !important; }

        /* Table Styles */
        .table-responsive { border-radius: 12px; overflow-x: auto; }
        .table thead th { border-top: none; background: #f8f9fa; color: #4b5563; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* Perfect Action Buttons */
        .btn-action { border-radius: 6px; font-weight: 600; font-size: 0.75rem; white-space: nowrap; min-width: 110px; transition: all 0.2s; }
        .btn-loyalty { color: #059669; border: 1px solid #059669; background: transparent; }
        .btn-loyalty:hover { background: #059669; color: #fff; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header"><h4>Aquil<span class="text-success">Life</span></h4></div>
    <div class="nav-links">
        <a href="admin_dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="admin_inbox.php"><i class="fas fa-envelope"></i> Contact Inbox</a>
        <a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Customer Orders</a>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
        <a href="manage_products.php" ><i class="fas fa-pills"></i> Products</a>
        <a href="admin_health.php" class="active"><i class="fas fa-heartbeat"></i> Customer Health</a>
        <a href="admin_analysis.php"><i class="fas fa-chart-line"></i> Business Analysis</a>
        <div class="dropdown-divider border-secondary mx-3"></div>
        <a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="top-bar d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-0 text-dark font-weight-bold"><i class="fas fa-heartbeat text-danger mr-2"></i>Customer Health Insights</h5>
        <span class="text-muted small"><i class="far fa-calendar-alt mr-1"></i> <?php echo date('d M Y'); ?></span>
    </div>

    <div class="row mt-4">
        <?php 
        $icons = ['Champion' => 'fa-crown', 'Healthy' => 'fa-shield-alt', 'At-Risk' => 'fa-user-clock', 'Needs Attention' => 'fa-exclamation-circle'];
        $classSuffix = ['Champion' => 'champion', 'Healthy' => 'healthy', 'At-Risk' => 'atrisk', 'Needs Attention' => 'needsattention'];
        $meanings = [
            'Champion' => 'High-value loyalists with frequent purchases.',
            'Healthy' => 'Consistent customers with stable engagement.',
            'At-Risk' => 'Inactive users with high churn probability.',
            'Needs Attention' => 'Active users with high complaint counts.'
        ];

        foreach($counts as $status => $val): ?>
            <div class="col-xl-3 col-md-6 col-sm-12">
                <div class="card health-card card-<?php echo $classSuffix[$status]; ?> text-white p-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 small opacity-75 font-weight-bold"><?php echo strtoupper($status); ?></p>
                            <h2 class="mb-0 font-weight-bold"><?php echo $val; ?></h2>
                        </div>
                        <i class="fas <?php echo $icons[$status]; ?> fa-2x opacity-50"></i>
                    </div>
                    <div class="mt-2 small opacity-75 d-none d-sm-block" style="line-height: 1.2;">
                        <?php echo $meanings[$status]; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="bg-white rounded-lg shadow-sm border mt-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="pl-4">Customer</th>
                        <th>Engagement</th>
                        <th>Support</th>
                        <th>Status</th>
                        <th class="text-right pr-4">Strategy</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data as $user): ?>
                    <tr>
                        <td class="pl-4">
                            <div class="font-weight-bold text-dark"><?php echo $user['FULL_NAME']; ?></div>
                            <div class="small text-muted"><?php echo $user['EMAIL']; ?></div>
                        </td>
                        <td>
                            <span class="text-dark small">Orders: <b><?php echo $user['TOTAL_ORDERS']; ?></b></span><br>
                            <span class="text-muted small">Revenue: <b>₹<?php echo number_format($user['TOTAL_SPENT'], 0); ?></b></span>
                        </td>
                        <td>
                            <span class="badge badge-light border text-muted">
                                <i class="far fa-comment-alt mr-1"></i><?php echo $user['SUPPORT_TICKETS']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge bg-<?php echo strtolower(str_replace(' ', '', $user['HEALTH_STATUS'])); ?>">
                                <?php echo $user['HEALTH_STATUS']; ?>
                            </span>
                        </td>
                        <td class="text-right pr-4">
                            <?php if($user['HEALTH_STATUS'] == 'Champion'): ?>
                                <button class="btn btn-sm btn-loyalty btn-action"><i class="fas fa-gift mr-1"></i> Loyalty Bonus</button>
                            <?php elseif($user['HEALTH_STATUS'] == 'At-Risk'): ?>
                                <button class="btn btn-sm btn-outline-warning btn-action">Win Back</button>
                            <?php elseif($user['HEALTH_STATUS'] == 'Needs Attention'): ?>
                                <button class="btn btn-sm btn-outline-danger btn-action">Resolve Now</button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-primary btn-action">Maintenance</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>