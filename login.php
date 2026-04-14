<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>
<link rel="stylesheet" href="css/login.css">

<div class="auth-container">
    <div class="card auth-card shadow" data-aos="zoom-in">
        <div class="auth-header">
            <h3 class="font-weight-bold text-white mb-1">Welcome Back</h3>
            <p class="text-white-50 small mb-0">Sign in to your Aquil Life account</p>
        </div>

        <div class="card-body">
            <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
                <div class="auth-error text-danger text-center small mb-3 p-2 border rounded bg-light">
                    <i class="fas fa-exclamation-circle mr-2"></i> Incorrect email or password.
                </div>
            <?php endif; ?>

            <form action="login_process.php" method="POST">
                <div class="form-group mb-3"> 
                    <label class="form-label text-uppercase">Email Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" name="email" class="form-control" placeholder="name@email.com" required>
                    </div>
                </div>

                <div class="form-group mb-3"> 
                    <label class="form-label text-uppercase">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required style="border-right:none;">
                        <div class="input-group-append" onclick="togglePassword()" style="cursor:pointer">
                            <span class="input-group-text bg-light" style="border-left:none;"><i class="fas fa-eye" id="eyeIcon"></i></span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center ">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="rememberMe" name="remember">
                        <label class="custom-control-label small text-muted" for="rememberMe">Remember me</label>
                    </div>
                    <a href="forgot_password.php" class="small font-weight-bold" style="color: var(--aquil-green)">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-auth shadow-sm ">
                    Log In <i class="fas fa-sign-in-alt "></i>
                </button>
            </form>
            <div class="text-center mt-2">
                <p class="text-muted small mb-0">New to Aquil Life? <a href="signup.php" class="font-weight-bold" style="color:var(--aquil-green)">Create Account</a></p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('passwordInput');
    const eyeIcon = document.getElementById('eyeIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<?php include 'footer.php'; ?>