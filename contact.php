
<?php 
// 1. MUST START SESSION FIRST
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 2. DEFINE VARIABLES BEFORE USE
$isLoggedIn = isset($_SESSION['user_role']); 
$userName = $isLoggedIn ? htmlspecialchars($_SESSION['full_name']) : 'Guest';

include 'header.php'; 
include 'nav.php'; 
$supportEmail = "support@aquillife.com";
$userMailSub = rawurlencode("Inquiry: Healthcare Support for " . $userName);
$userMailBody = rawurlencode("Hello Aquil Life Team,\n\nI have the following inquiry regarding your services:\n\n[Type your message here]\n\nRegards,\n" . $userName);
$userGmailUrl = "https://mail.google.com/mail/?view=cm&fs=1&to=$supportEmail&su=$userMailSub&body=$userMailBody";
?>

<style>
    :root {
        --aquil-navy: #0a3d62;
        --aquil-green: #2a7f62;
        --soft-bg: #f4f7f6;
    }
    body { background-color: var(--soft-bg); }
    .contact-wrapper { padding: 20px 0; margin-bottom:30px;}
    .contact-card {
        border: none;
        border-radius: 24px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .info-sidebar {
        background: linear-gradient(135deg, var(--aquil-navy), var(--aquil-green));
        color: white;
        padding: 50px;
        height: 100%;
    }
    .contact-icon-box {
        width: 50px; height: 50px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin-right: 20px; font-size: 1.2rem;
    }
    .form-container { padding: 40px; }
    .aquil-input {
        border-radius: 12px; padding: 12px 18px;
        background: #f8fafc; border: 2px solid #e2e8f0;
        color: #1e293b !important; font-weight: 500;
        transition: 0.3s; height: auto;
    }
    .btn-aquil {
        background: var(--aquil-green); color: white;
        border: none; padding: 15px; border-radius: 12px;
        font-weight: 700; transition: 0.3s;
    }
    .btn-aquil:hover {
        background: var(--aquil-navy);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(42, 127, 98, 0.2);
    }

    .gmail-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #1a73e8; /* Google Blue */
        color: #FFF !important;
        border-radius: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(26, 115, 232, 0.4);
        transition: all 0.3s ease;
        text-decoration: none !important;
    }
    .gmail-float:hover {
        transform: translateY(-5px);
        background-color: #1557b0;
        box-shadow: 0 8px 20px rgba(26, 115, 232, 0.6);
    }
</style>
<a href="<?php echo $userGmailUrl; ?>" class="gmail-float" target="_blank" title="Need technical help? Email us directly">
    <i class="fas fa-envelope"></i>
</a>
<?php if(isset($_GET['status']) && $_GET['status'] == 'sent'): ?>
    <div class="container mt-4" >
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 15px;">
            <i class="fas fa-check-circle mr-2"></i>
            <strong>Thank you, <?php echo htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8'); ?>!</strong> 
            Your inquiry has been received.
        </div>
    </div>
<?php endif; ?>

<?php if(isset($_GET['status']) && $_GET['status'] == 'error'): ?>
    <div class="container mt-4">
        <div class="alert alert-danger shadow-sm border-0" style="border-radius: 15px;">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Oops! Something went wrong. Please try again later.
        </div>
    </div>
<?php endif; ?>

<section class="contact-wrapper">
    <div class="container">
        <div class="contact-header text-center mb-3" data-aos="fade-down">
            <h1 class="font-weight-bold" style="color: var(--aquil-navy);">Contact Us</h1>
            <p class="text-muted">Reach out to <span style="color: var(--aquil-green); font-weight:600;">Aquil Life</span> for professional guidance.</p>
            <div class="title-divider mx-auto"></div>
        </div>

        <div class="contact-card" data-aos="fade-up">
            <div class="row no-gutters">
                <div class="col-lg-5">
                    <div class="info-sidebar">
                        <h3 class="font-weight-bold mb-4">Let's Talk</h3>
                        <p class="mb-5 opacity-75">Our team is dedicated to listening and understanding every patient.</p>
                        
                        <div class="d-flex align-items-center mb-4">
                            <div class="contact-icon-box"><i class="fas fa-phone"></i></div>
                            <div>
                                <small class="d-block opacity-75">Phone</small>
                                <strong class="h6">+91 98765 43210</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-4">
                            <div class="contact-icon-box"><i class="fas fa-envelope"></i></div>
                            <div>
                                <small class="d-block opacity-75">Email</small>
                                <strong class="h6">support@aquillife.com</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-4">
                            <div class="contact-icon-box"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <small class="d-block opacity-75">Clinic</small>
                                <strong class="h6">Mumbai, Maharashtra, India</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="form-container">
                        <form action="contact_process.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>FULL NAME</label>
                                    <input type="text" name="name" class="form-control aquil-input" 
                                        value="<?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : ''; ?>" 
                                        placeholder="Enter name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>EMAIL ADDRESS</label>
                                    <input type="email" name="email" class="form-control aquil-input" 
                                        value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" 
                                        placeholder="Enter email" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>SUBJECT</label>
                                <select name="subject" class="form-control aquil-input" required>
                                    <option value="" disabled selected>Choose Inquiry Type</option>
                                    <option value="General Inquiry">General Inquiry</option>
                                    <option value="Nutrition Consultation">Nutrition Consultation</option>
                                    <option value="Product Support">Product Support</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label>MESSAGE</label>
                                <textarea name="message" class="form-control aquil-input" rows="3" placeholder="How can we help you?" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-aquil btn-block shadow-sm">
                                Send Message <i class="fas fa-paper-plane ml-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    if ($(".alert").length > 0) {
        setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 3000);
    }
});
</script>
<?php include 'footer.php'; ?>