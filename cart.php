<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

$profileIncomplete = false;
if (isset($_SESSION['user_email'])) {
    $userEmail = $_SESSION['user_email'];
    $checkQuery = "SELECT MOBILE, LOCATION FROM SYSTEM.AQUIL_ACCOUNTS WHERE EMAIL = :email";
    $stid = oci_parse($conn, $checkQuery);
    oci_bind_by_name($stid, ":email", $userEmail);
    oci_execute($stid);
    $user = oci_fetch_array($stid, OCI_ASSOC);
    $profileIncomplete = (empty($user['MOBILE']) || empty($user['LOCATION']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $newItem = [
            'id'    => $_POST['id'],
            'name'  => $_POST['name'],
            'price' => (float)$_POST['price'],
            'qty'   => (int)$_POST['qty'],
            'img'   => $_POST['img']
        ];
        if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['name'] == $newItem['name']) {
                $item['qty'] += $newItem['qty'];
                $found = true;
                break;
            }
        }
        if (!$found) { $_SESSION['cart'][] = $newItem; }
        echo json_encode(['status' => 'success', 'cart_count' => count($_SESSION['cart'])]);
        exit;
    }

    if ($_POST['action'] == 'update_qty') {
        $productName = $_POST['name'];
        $newQty = (int)$_POST['qty'];
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['name'] == $productName) {
                    $item['qty'] = max(1, $newQty);
                    break;
                }
            }
        }
        echo json_encode(['status' => 'success']);
        exit;
    }

    if ($_POST['action'] == 'remove') {
        $productName = $_POST['name'];
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['name'] == $productName) {
                    unset($_SESSION['cart'][$key]);
                    $_SESSION['cart'] = array_values($_SESSION['cart']);
                    break;
                }
            }
        }
        echo json_encode(['status' => 'success']);
        exit; 
    }
}

include 'header.php'; 
include 'nav.php'; 
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7f9; color: #1e293b; }
    .cart-header-section { padding: 40px 0 20px; }
    .cart-title { font-weight: 800; color: #0a3d62; letter-spacing: -1px; margin-bottom: 5px; }
    .cart-subtitle { color: #64748b; font-size: 1.1rem; }
    .cart-item-card { background: #ffffff; border-radius: 20px; border: 1px solid rgba(0,0,0,0.05) !important; margin-bottom: 20px; }
    .cart-img-container { width: 80px; height: 80px; border-radius: 12px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .cart-img-container img { width: 100%; height: 100%; object-fit: contain; }
    .qty-control { display: flex; align-items: center; background: #f1f5f9; border-radius: 10px; width: fit-content; padding: 2px; }
    .qty-btn { border: none; background: transparent; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; color: #0a3d62; font-weight: bold; transition: 0.2s; }
    .qty-btn:hover { background: #e2e8f0; border-radius: 8px; }
    .qty-val { width: 40px; text-align: center; font-weight: 700; color: #0a3d62; border: none; background: transparent; }
    .summary-card { border-radius: 24px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .btn-confirm { background: linear-gradient(135deg, #0a3d62, #2a7f62); color: white !important; border-radius: 15px; font-weight: 700; padding: 16px; width: 100%; transition: 0.3s; border: none; }
    .btn-confirm:hover { transform: scale(1.02); filter: brightness(1.1); }
    /* Modal Styles */
    .aquil-modal-content { border-radius: 24px; border: none; overflow: hidden; }
    .aquil-modal-header { background: linear-gradient(135deg, #0a3d62, #2a7f62); color: white; border: none; padding: 25px; }
    .btn-aquil-primary { background: #0a3d62; color: white !important; border-radius: 12px; font-weight: 700; transition: 0.3s; padding: 12px; border: none; text-align: center; display: block; width: 100%; }
</style>

<div class="container min-vh-100 mb-5">
    <div class="cart-header-section" data-aos="fade-down">
        <h1 class="cart-title">My Cart</h1>
        <p class="cart-subtitle">Review your items and complete your health journey.</p>
    </div>

    <?php if(!empty($_SESSION['cart'])): ?>
    <div class="row">
        <div class="col-lg-8">
            <?php 
            $grandTotal = 0;
            foreach($_SESSION['cart'] as $item): 
                $total = $item['price'] * $item['qty'];
                $grandTotal += $total;
            ?>
            <div class="card cart-item-card shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <div class="row align-items-center">
                        <div class="col-3 col-md-2">
                            <div class="cart-img-container">
                                <img src="<?php echo $item['img']; ?>" alt="Product">
                            </div>
                        </div>
                        <div class="col-9 col-md-4">
                            <h6 class="mb-1 font-weight-bold"><?php echo $item['name']; ?></h6>
                            <p class="text-muted small mb-2">₹<?php echo number_format($item['price'], 2); ?></p>
                            <div class="qty-control">
                                <button class="qty-btn update-qty" data-name="<?php echo $item['name']; ?>" data-delta="-1"><i class="fas fa-minus small"></i></button>
                                <input type="text" class="qty-val" value="<?php echo $item['qty']; ?>" readonly>
                                <button class="qty-btn update-qty" data-name="<?php echo $item['name']; ?>" data-delta="1"><i class="fas fa-plus small"></i></button>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 text-md-right mt-3 mt-md-0">
                            <span class="d-block text-muted small">Subtotal</span>
                            <span class="h5 font-weight-bold" style="color: #0a3d62;">₹<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="col-6 col-md-2 text-right">
                            <button class="btn btn-light btn-sm text-danger remove-item" data-name="<?php echo $item['name']; ?>" style="border-radius: 10px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <a href="services.php" class="btn text-muted font-weight-bold mb-5 mt-2">
                <i class="fas fa-chevron-left mr-2"></i> Continue Exploring
            </a>
        </div>

        <div class="col-lg-4">
            <div class="card summary-card p-2 sticky-top" style="top: 100px;">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="font-weight-bold">₹<?php echo number_format($grandTotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">Shipping</span>
                        <span class="text-success font-weight-bold">FREE</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="h5 font-weight-bold">Total</span>
                        <span class="h3 font-weight-bold text-success">₹<?php echo number_format($grandTotal, 2); ?></span>
                    </div>

                    <?php if (isset($_SESSION['user_email'])): ?>
                        <form id="orderForm" action="confirm_order.php" method="POST">
                            <button type="button" id="btnCheckProfile" class="btn-confirm shadow-lg">
                                Confirm Order <i class="fas fa-check-circle ml-2"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php?redirect=cart.php" class="btn btn-confirm">Login to Confirm</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <img src="https://cdn-icons-png.flaticon.com/512/11329/11329973.png" style="width: 120px; opacity: 0.5;">
        <h3 class="mt-4">Your cart is empty</h3>
        <a href="services.php" class="btn btn-primary mt-3">Start Shopping</a>
    </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="profileRequiredModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content aquil-modal-content shadow-lg">
            <div class="modal-header aquil-modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-edit mr-2"></i> Action Required</h5>
            </div>
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-map-marker-alt fa-4x text-danger mb-3"></i>
                    <h4 class="font-weight-bold text-dark">Missing Delivery Info</h4>
                    <p class="text-muted">We need your <strong>Mobile Number</strong> and <strong>Location</strong> to process your order correctly.</p>
                </div>
                <div class="d-flex flex-column" style="gap: 10px;">
                    <a href="update_profile.php?redirect=cart.php" class="btn btn-aquil-primary">
                        Complete Profile Now <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                    <button type="button" class="btn btn-link text-muted small" data-dismiss="modal">Return to Cart</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="finalConfirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content aquil-modal-content shadow-lg">
            <div class="modal-header aquil-modal-header">
                <h5 class="modal-title"><i class="fas fa-shopping-basket mr-2"></i> Final Confirmation</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 text-center">
                <h5 class="font-weight-bold mb-3">Place your order?</h5>
                <p class="text-muted">You are about to confirm your order. A digital receipt will be generated upon confirmation.</p>
                <div class="alert alert-secondary py-2">
                    <small>Payment Mode: <strong>Cash on Delivery</strong></small>
                </div>
                <div class="d-flex flex-column" style="gap: 10px;">
                    <button id="executeOrderBtn" class="btn btn-aquil-primary py-3">
                        Yes, Place Order <i class="fas fa-check-circle ml-2"></i>
                    </button>
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Wait, let me check</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $('#btnCheckProfile').on('click', function(e) {
        e.preventDefault();
        
        <?php if ($profileIncomplete): ?>
            // Show profile missing modal if data is empty
            $('#profileRequiredModal').modal('show');
        <?php else: ?>
            // Set the target and show the final confirmation modal
            orderTargetUrl = "confirm_order.php"; 
            $('#finalConfirmModal').modal('show');
        <?php endif; ?>
    });

    // 2. Quantity Update Logic (Stays Same)
    $('.update-qty').on('click', function() {
        const name = $(this).data('name');
        const delta = parseInt($(this).data('delta'));
        const currentQty = parseInt($(this).siblings('.qty-val').val());
        const newQty = currentQty + delta;
        if (newQty >= 1) {
            $.post('cart.php', { action: 'update_qty', name: name, qty: newQty }, function() {
                location.reload();
            });
        }
    });

    // 3. Remove Logic (Stays Same)
    $('.remove-item').on('click', function() {
        const name = $(this).data('name');
        $.post('cart.php', { action: 'remove', name: name }, function() {
            location.reload();
        });
    });

    // 4. THE FINAL EXECUTION (Shared by both Services and Cart)
// 4. THE FINAL EXECUTION
$('#executeOrderBtn').on('click', function() {
    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Finalizing...');

    // Extract ID/Qty from the URL we saved earlier (for "Order Now" from Services)
    let urlParams = new URLSearchParams(orderTargetUrl.split('?')[1]);
    let directId = urlParams.get('id');
    let directQty = urlParams.get('qty');

    $.ajax({
        url: 'confirm_order.php',
        method: 'POST',
        data: { 
            product_id: directId, 
            qty: directQty 
        },
success: function(response) {
    try {
        // Force the response to be treated as a string then parsed
        // This handles cases where jQuery might have already parsed it or not
        let data = (typeof response === 'object') ? response : JSON.parse(response);

        if(data.status === 'success' && data.order_id) {
            // Success! Send them to the receipt
            window.location.href = 'receipt.php?id=' + data.order_id;
        } else {
            Swal.fire('Error', data.message || 'Processing failed', 'error');
            $btn.prop('disabled', false).text('Yes, Place Order');
        }
    } catch (e) {
        // If parsing fails, the order might still have been placed
        // This is a safety fallback for your demo
        console.error("Parse Error:", e);
        window.location.href = 'user_dashboard.php'; 
    }
}
    });
});
});
</script>