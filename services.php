<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php'; 
include 'header.php';
include 'nav.php';
$query = "SELECT PRODUCT_ID, NAME, CATEGORY, PRICE, STOCK, IMAGE_URL, DESCRIPTION FROM SYSTEM.AQUIL_PRODUCTS WHERE STOCK >= 0 ORDER BY NAME ASC";
$stid = oci_parse($conn, $query);
oci_execute($stid);
$isLoggedIn = isset($_SESSION['user_id']) ? 'true' : 'false';
?>
<style>
/* New UI Styling */
.shop-section { background-color: #f8f9fa; }
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
    border: none;
    cursor: pointer;
}
.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15) !important;
}
.section-title {
    font-size: 2.5rem;
    letter-spacing: 1px;
    color: #0a3d62;
}
.badge-status {
    padding: 6px 12px;
    font-size: 0.75rem;
    border-radius: 12px;
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 2;
}
.search-container input, .search-container select {
    border: none;
    height: 50px;
}
</style>

<section class="shop-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title font-weight-bold">Our Premium Commodities</h2>
            <p class="text-muted">Browse supplements, kits, and essentials curated for your health.</p>
            
            <div class="row justify-content-center mt-4">
                <div class="col-md-5">
                    <div class="input-group shadow-sm">
                        <input type="text" id="commoditySearch" class="form-control rounded-pill px-4" placeholder="Search products...">
                    </div>
                </div>
                <div class="col-md-3 mt-3 mt-md-0">
                    <select id="categoryFilter" class="form-control rounded-pill shadow-sm px-4">
                        <option value="">All Categories</option>
                        <?php 
                        // Fetch categories dynamically for the filter
                        $cat_res = oci_parse($conn, "SELECT DISTINCT CATEGORY FROM SYSTEM.AQUIL_PRODUCTS WHERE CATEGORY IS NOT NULL");
                        oci_execute($cat_res);
                        while ($cat = oci_fetch_array($cat_res, OCI_ASSOC)) {
                            echo '<option value="'.htmlspecialchars($cat['CATEGORY']).'">'.htmlspecialchars($cat['CATEGORY']).'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row" id="productGrid">
            <?php 
            // Reset pointer if query was already run
            oci_execute($stid); 
            while ($row = oci_fetch_array($stid, OCI_ASSOC)): 
                $imagePath = !empty($row['IMAGE_URL']) ? $row['IMAGE_URL'] : 'product_img/default.webp';
                $priceLabel = "₹" . number_format($row['PRICE'], 2);
                $stock = intval($row['STOCK']);
                $jsDesc = addslashes(str_replace(["\r", "\n"], ' ', $row['DESCRIPTION'] ?? "Premium quality commodity."));
            ?>
            <div class="col-lg-3 col-md-6 mb-4 product-item" data-category="<?php echo htmlspecialchars($row['CATEGORY']); ?>">
                <div class="card shadow-sm h-100 product-card" 
                     onclick="openProductModal('<?php echo $row['PRODUCT_ID']; ?>', '<?php echo addslashes($row['NAME']); ?>', '<?php echo $jsDesc; ?>', '<?php echo $priceLabel; ?>', '<?php echo $imagePath; ?>', '<?php echo $stock; ?>')">
                    
                    <div class="position-relative text-center bg-white p-3" style="border-radius: 15px 15px 0 0;">
                        <img src="<?php echo $imagePath; ?>" class="card-img-top" style="height:200px; object-fit:contain;">
                        <?php if ($stock > 0): ?>
                            <span class="badge badge-success badge-status">In Stock</span>
                        <?php else: ?>
                            <span class="badge badge-danger badge-status">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body text-center d-flex flex-column">
                        <h6 class="text-muted small mb-1"><?php echo htmlspecialchars($row['CATEGORY']); ?></h6>
                        <h5 class="card-title font-weight-bold mb-2"><?php echo htmlspecialchars($row['NAME']); ?></h5>
                        <h5 class="text-primary font-weight-bold mt-auto"><?php echo $priceLabel; ?></h5>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<div class="modal fade" id="productDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <img id="modalImg" src="" class="img-fluid rounded shadow-sm" alt="Product Image">
                    </div>
                    <div class="col-md-7">
                        <h3 id="modalName" class="font-weight-bold mb-2"></h3>
                        <p id="modalDesc" class="text-muted small mb-1"></p>
                        
                        <div id="modalStockDisplay" class="mb-3 small"></div>
                        <h4 id="modalPrice" class="text-primary font-weight-bold mb-4"></h4>

                        <div class="qty-selector mb-3">
                            <span class="mr-2">Quantity:</span>
                            <input type="number" id="modalQty" value="1" min="1" class="form-control d-inline-block" style="width: 70px;">
                        </div>
                        <div class="d-flex flex-column" style="gap: 10px;">
                            <button id="addToCartBtn" class="btn btn-outline-primary btn-block shadow-sm">
                                Add to Cart <i class="fas fa-cart-plus ml-2"></i>
                            </button>
                            <button id="orderNowBtn" class="btn btn-primary btn-block shadow-sm">
                                Order Now <i class="fas fa-bolt ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="completeProfileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content aquil-modal-content shadow-lg">
            <div class="modal-header aquil-modal-header">
                <h5 class="modal-title"><i class="fas fa-user-shield mr-2"></i> Security Verification</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-map-marked-alt fa-4x" style="color: #0a3d62;"></i>
                </div>
                <h4 class="font-weight-bold">Delivery Details Needed</h4>
                <p class="text-muted">To ensure your health products reach the correct address, please provide your contact and location details.</p>
                
                <div class="d-flex flex-column mt-4" style="gap: 12px;">
                    <a href="update_profile.php?redirect=shop.php" class="btn btn-aquil-primary py-2">
                        Update Profile Now <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">I'll do it later</button>
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
    var USER_LOGGED_IN = <?php echo isset($_SESSION['user_email']) ? 'true' : 'false'; ?>;

    $(document).ready(function() {
        console.log("Is User Logged In?:", USER_LOGGED_IN);
    });
    $(document).ready(function() {
    // Combined Search and Filter Logic
    function filterProducts() {
        var searchVal = $("#commoditySearch").val().toLowerCase();
        var catVal = $("#categoryFilter").val().toLowerCase();

        $("#productGrid .product-item").each(function() {
            var cardText = $(this).text().toLowerCase();
            var cardCategory = ($(this).data('category') || "").toLowerCase();
            
            var matchesSearch = cardText.indexOf(searchVal) > -1;
            var matchesCategory = catVal === "" || cardCategory === catVal;

            if (matchesSearch && matchesCategory) {
                $(this).fadeIn(200);
            } else {
                $(this).hide();
            }
        });
    }

    $("#commoditySearch").on("keyup", filterProducts);
    $("#categoryFilter").on("change", filterProducts);
});
    $(document).on('click', '#executeOrderBtn', function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        let dId, dQty;
        if (orderTargetUrl) {
            let urlParams = new URLSearchParams(orderTargetUrl.split('?')[1]);
            dId = urlParams.get('id');
            dQty = urlParams.get('qty');
        } else {
            dId = currentProductId;
            dQty = $('#modalQty').val();
        }

        $.ajax({
            url: 'confirm_order.php',
            method: 'POST',
            data: { 
                product_id: dId, 
                qty: dQty 
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
        $("#commoditySearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#productGrid .product-item").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    });
</script>


