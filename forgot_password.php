<?php include 'header.php'; include 'nav.php'; ?>
<link rel="stylesheet" href="css/login.css">

<div class="auth-container">
    <div class="card auth-card shadow" data-aos="zoom-in">
        <div class="auth-header text-center">
            <h3 class="font-weight-bold text-white mb-1">Reset Password</h3>
            <p class="text-white-50 small mb-0">We will send an OTP to your registered email</p>
        </div>

        <div class="card-body">
            <form id="forgotForm">
                <div class="form-group mb-4">
                    <label class="form-label">EMAIL ADDRESS</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="name@email.com" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-auth shadow-sm w-100">
                    Send Recovery Code <i class="fas fa-paper-plane ml-2"></i>
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="login.php" class="small font-weight-bold" style="color: var(--aquil-green)">Back to Login</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('forgotForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formElement = this;
    const formData = new FormData(formElement);
    const userEmail = formData.get('email'); 
    const container = document.querySelector('.auth-container');

    Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

    fetch('forgot_process.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: 'Practice Mode: OTP is ' + data.otp, 
                text: 'Account found for ' + userEmail,
                icon: 'success',
                confirmButtonColor: '#004a61',
                confirmButtonText: 'Proceed to Reset',
                didOpen: () => {
                    if(container) container.style.filter = 'blur(10px)';
                },
                willClose: () => {
                    if(container) container.style.filter = 'none';
                }
            }).then(() => {
                window.location.href = 'recover.php?email=' + encodeURIComponent(userEmail);
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message,
                icon: 'error'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({ title: 'System Error', text: 'Check console for details', icon: 'error' });
    });
});
</script>
<?php include 'footer.php'; ?>