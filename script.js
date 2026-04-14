// Move this to the very top for global scope
let currentProductId = null; 
let orderTargetUrl = "";

$(document).ready(function() {

    // 1. GLOBAL FUNCTION TO OPEN MODAL
    // We attach it to 'window' so the onclick in your PHP can see it
    window.openProductModal = function(id, name, desc, price, img, stock) {
        currentProductId = id; 
        
        // Populate Modal Fields
        $('#modalName').text(name);     
        $('#modalDesc').html(desc); 
        $('#modalPrice').text(price);   
        $('#modalImg').attr('src', img); 

        const stockNum = parseInt(stock);
        if (stockNum > 0) {
            $('#modalStockDisplay').html('<span class="text-success">In Stock (' + stock + ' available)</span>');
            $('#modalQty').prop('disabled', false).val(1).attr('max', stock);
            $('#orderNowBtn, #addToCartBtn').show();
        } else {
            $('#modalStockDisplay').html('<span class="text-danger">Currently Out of Stock</span>');
            $('#modalQty').prop('disabled', true).val(0);
            $('#orderNowBtn, #addToCartBtn').hide();
        }

        // Show modal and FIX the aria-hidden focus error
        $('#productDetailModal').modal('show').removeAttr('aria-hidden');
    };

    // 2. ADD TO CART LOGIC
    $(document).on('click', '#addToCartBtn', function(e) {
        e.preventDefault();
        sendToCart(false); // false means don't redirect
    });

    // 3. ORDER NOW LOGIC
    $(document).on('click', '#orderNowBtn', function(e) {
        e.preventDefault();

        // Check Login Status
        if (typeof USER_LOGGED_IN === 'undefined' || USER_LOGGED_IN === false) {
            $('#productDetailModal').modal('hide'); 
            Swal.fire({
                title: 'Sign In Required',
                text: "Please login to place an order.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Login Now'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = "login.php?redirect=services.php";
            });
            return; 
        }

        // Profile Completeness Check via AJAX
        $.ajax({
            url: 'check_profile_data.php',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.profile_complete === false) {
                    $('#productDetailModal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('#completeProfileModal').modal('show');
                } else {
                    sendToCart(true); // true means redirect to confirm_order.php
                }
            }
        });
    });

    // 4. SHARED SEND TO CART FUNCTION
    function sendToCart(redirectImmediately) {
        if (!currentProductId) {
            alert("Error: No product selected.");
            return;
        }

        const productData = {
            action: 'add',
            id: currentProductId, 
            name: $('#modalName').text().trim(),
            price: $('#modalPrice').text().replace('₹', '').replace(',', '').trim(),
            qty: $('#modalQty').val(),
            img: $('#modalImg').attr('src')
        };

        $.post('cart.php', productData, function(response) {
            try {
                const res = JSON.parse(response);
                if(res.status === 'success') {
                    if(redirectImmediately) { 
                        $('#productDetailModal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('#finalConfirmModal').modal('show');
                        orderTargetUrl = "confirm_order.php?id=" + currentProductId + "&qty=" + productData.qty;
                    } else {
                        if($('#cart-badge').length) $('#cart-badge').text(res.cart_count);
                        $('#productDetailModal').modal('hide');
                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            icon: 'success',
                            title: productData.name + ' added to cart!',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                }
            } catch (e) { console.error("Parse Error", e); }
        });
    }
});

let hideTimeout; 

function togglePassword() {
    const passwordInput = document.getElementById('passwordInput');
    const eyeIcon = document.getElementById('eyeIcon');
    
    clearTimeout(hideTimeout);

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');

        hideTimeout = setTimeout(() => {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }, 500); 

    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}