<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>
<link rel="stylesheet" href="css/login.css">

<div class="auth-container">
    <div class="card auth-card signup-card shadow" data-aos="zoom-in">
        <div class="auth-header">
            <h3 class="font-weight-bold text-white mb-1">Create Account</h3>
            <p class="text-white-50 small mb-0">Join the Aquil Life community</p>
        </div>

        <div class="card-body">
            <form action="signup_process.php" method="POST">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="fullname" class="form-control" placeholder="John Doe" required>
                        </div>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="name@email.com" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" name="phone" class="form-control" placeholder="9876543210" required>
                        </div>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="form-label">Location</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="location" class="form-control" placeholder="City, State" required>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="passwordInput" class="form-control" style="border-right:none;" placeholder="••••••••" required>
                        <span class="input-group-text password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-auth shadow-sm">
                    Register Now <i class="fas fa-user-plus ml-2"></i>
                </button>
            </form>

            <div class="text-center mt-3">
                <p class="text-muted small mb-0">Already a member? <a href="login.php" class="font-weight-bold" style="color:var(--aquil-green)">Login Here</a></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Toggle Password Function
function togglePassword() {
    const passwordInput = document.getElementById('passwordInput');
    const eyeIcon = document.getElementById('eyeIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        eyeIcon.className = 'fas fa-eye';
    }
}

// AJAX Form Submission for the Blurred Popup Effect
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault(); // Stop the page from reloading
    
    const formData = new FormData(this);

    fetch('signup_process.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // We expect JSON back from PHP
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: 'Account Created!',
                text: 'Welcome to the Aquil Life community.',
                icon: 'success',
                confirmButtonColor: '#004a61',
                confirmButtonText: 'Go to Login',
                backdrop: `rgba(0,0,0,0.4) url("") left top no-repeat`, // Standard dimming
                didOpen: () => {
                    // Apply blur to your container
                    document.querySelector('.auth-container').style.filter = 'blur(8px)';
                },
                willClose: () => {
                    // Remove blur
                    document.querySelector('.auth-container').style.filter = 'none';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        } else {
            Swal.fire({
                title: 'Registration Failed',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>

<?php include 'footer.php'; ?>