<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$is_admin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'); 

$profilePic = isset($_SESSION['profile_pic']) && !empty($_SESSION['profile_pic']) 
              ? $_SESSION['profile_pic'] 
              : 'product_img/default_user.jpg';
$sessionPic = $_SESSION['profile_pic'] ?? '';
if (empty($sessionPic) || !file_exists($sessionPic)) {
    $profilePic = 'product_img/default_user.jpg';
} else {
    $profilePic = $sessionPic;
}
?>

<script>
function confirmLogout() {
    Swal.fire({
        icon: 'warning', 
        title: 'Sign Out of Aquil Life?',
        html: `
            <div class="text-center">
                <p class="text-secondary mb-2" style="font-size: 0.95rem;">Are you sure you want to end your session?</p>
                <p class="small text-muted">
                    Logged in as <span style="color: #2a7f62; font-weight: 700;">
                    <?php echo $_SESSION['user_name'] ?? "Shriya Ghonge"; ?>
                    </span>
                </p>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'LOG OUT',
        showCloseButton: true,
        background: '#ffffff',
        customClass: {
            popup: 'aquil-logout-popup',
            confirmButton: 'btn-aquil-logout'
        },
        buttonsStyling: false,
        didOpen: () => {
            // Apply background blur
            const bgElements = document.querySelectorAll('#navbar, main, footer, .hero-section, .auth-container');
            bgElements.forEach(el => {
                el.style.filter = 'blur(12px)';
                el.style.transition = 'filter 0.3s ease';
            });
        },
        willClose: () => {
            const bgElements = document.querySelectorAll('#navbar, main, footer, .hero-section, .auth-container');
            bgElements.forEach(el => el.style.filter = 'none');
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        }
    });
}
</script>

<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top py-0" id="navbar" style="box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <a class="navbar-brand" href="index.php" style="margin-left: 100px;">
        <img src="product_img/no_tag_logo.png" alt="Aquil Life Logo" height="60px">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto align-items-center"> <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
            <li class="nav-item"><a class="nav-link" href="services.php">Commodities </a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            
            <li class="nav-item ml-lg-2">
                <a href="cart.php" class="nav-link position-relative">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                    <span id="cart-badge" class="badge badge-primary badge-pill position-absolute" style="top: -5px; right: -5px;">
                        <?php echo $cartCount; ?>
                    </span>
                </a>
            </li>
                
            <?php if(isset($_SESSION['user_email'])): ?>
                <li class="nav-item dropdown ml-lg-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center py-0" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?php echo $profilePic; ?>" 
                        alt="Profile" 
                        class="rounded-circle mr-2 shadow-sm" 
                        style="width: 35px; height: 35px; object-fit: cover; border: 1px solid #eee;">
                        
                        <span class="<?php echo $is_admin ? 'text-success' : 'text-primary'; ?> font-weight-bold">
                            <?php echo $_SESSION['user_name'] ?? 'User'; ?>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow border-0" aria-labelledby="navbarDropdown">
                        <?php if($is_admin): ?>
                            <a class="dropdown-item" href="Admin/admin_dashboard.php">
                                <i class="fas fa-user-shield mr-2"></i>Admin Panel
                            </a>
                        <?php else: ?>
                            <a class="dropdown-item" href="user_dashboard.php">
                                <i class="fas fa-user mr-2"></i>My Dashboard
                            </a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmLogout()">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                    </div>
                </li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <li class="nav-item">
                    <a class="btn btn-primary ml-lg-3 px-4" href="signup.php" style="border-radius: 20px;">Join Now</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

