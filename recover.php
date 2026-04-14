<?php include 'header.php'; include 'nav.php'; ?>
<link rel="stylesheet" href="css/login.css">
<?php $email = $_GET['email'] ?? ''; ?>

<div class="auth-container">
    <div class="card auth-card shadow" data-aos="zoom-in">
        <div class="auth-header text-center">
            <h3 class="text-white">Verify & Reset</h3>
            <p class="text-white-50 small">Enter the code you just saw in the popup</p>
        </div>
        <div class="card-body">
            <form id="recoverForm">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                
                <div class="form-group mb-3">
                    <label class="form-label">RECOVERY CODE</label>
                    <input type="text" name="otp" class="form-control" placeholder="Enter 4-digit code" required>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label">NEW PASSWORD</label>
                    <input type="password" name="new_password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-auth w-100">Update Password</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('recoverForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const container = document.querySelector('.auth-container');

    fetch('verify_recovery.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#004a61',
                didOpen: () => { container.style.filter = 'blur(10px)'; }
            }).then(() => {
                window.location.href = 'login.php';
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    });
});
</script>
<?php include 'footer.php'; ?>