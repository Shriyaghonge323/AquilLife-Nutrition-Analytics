<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
include 'db.php';
include 'header.php'; 
include 'nav.php'; 
?>

<?php if (isset($_GET['login']) && $_GET['login'] == 'success'): ?>
<script>
    Swal.fire({
        title: 'Welcome Back!',
        text: 'Successfully logged in to AquilLife.',
        icon: 'success',
        showConfirmButton: false,
        timer: 1500,
        background: '#ffffff',
        iconColor: '#2a7f62', 
        customClass: {
            title: 'font-weight-bold',
            popup: 'rounded-lg'
        }
    });
    window.history.replaceState({}, document.title, "index.php");
</script>
<?php endif; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('login') === 'success') {
            Swal.fire({
                title: 'Welcome Back!',
                text: 'Successfully logged in to AquilLife.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                iconColor: '#2a7f62'
            });
            window.history.replaceState({}, document.title, "index.php");
        }

        // Review Success Alert
        if (urlParams.get('review') === 'success') {
            Swal.fire({
                title: 'Review Posted!',
                text: 'Thank you for sharing your experience with our community.',
                icon: 'success',
                confirmButtonColor: '#2a7f62'
            });
            window.history.replaceState({}, document.title, "index.php");
        }
    });
</script>

<section id="home" class="hero text-white text-left d-flex align-items-center">
    <div class="container">
        <div class="row pb-5"> <div class="col-md-7 col-lg-6" data-aos="fade-right">
                <div class="hero-content-wrapper">
                    <?php if(isset($_SESSION['user_email'])): ?>
                        <span class="badge badge-success mb-3 px-3 py-2">ACTIVE MEMBER</span>
                        <h2 class="display-4 font-weight-bold mb-2">
                            Welcome Back, <br>
                            <span class="text-info"><?php echo explode('@', $_SESSION['user_email'])[0]; ?>!</span>
                        </h2>
                        <p class="lead mb-4">
                            Ready to continue your wellness journey? Check your insights or explore supplements.
                        </p>
                        <div class="hero-btns d-flex">
                            <a href="user_dashboard.php" class="btn btn-outline-light btn-lg mr-3 shadow-lg">My Dashboard</a>
                            <a href="services.php" class="btn btn-primary btn-lg">Order Now</a>
                        </div>
                    <?php else: ?>
                        <h1 class="display-4 font-weight-bold mb-3">Nourishing Lives.<br> Building a Future.</h1>
                        <p class="lead mb-4">Empowering women and children through accessible nutrition.</p>
                        <div class="hero-btns d-flex">
                            <a href="signup.php" class="btn btn-outline-light btn-lg mr-3">Get Started</a>
                            <a href="services.php" class="btn btn-primary btn-lg">Explore Products</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if(isset($_SESSION['user_email'])): ?>
<section class="quick-stats py-4 shadow-sm" style="background: #fff; margin-top: -30px; position: relative; z-index: 10; border-radius: 15px;">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 border-right">
                <small class="text-muted d-block">Current Status</small>
                <span class="font-weight-bold text-success"><i class="fas fa-check-circle"></i> Account Active</span>
            </div>
            <div class="col-md-4 border-right">
                <small class="text-muted d-block">Preferred Location</small>
                <span class="font-weight-bold"><?php echo $_SESSION['user_location'] ?? 'Not Set'; ?></span>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block">Quick Support</small>
                <a href="contact.php" class="font-weight-bold text-primary">Chat with Expert</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="dr-features-fluid">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-subtitle font-weight-bold">Professional Excellence</span>
            <h2 class="section-title">Why Choose Our Care</h2>
        </div>

        <div class="row align-items-center">
            <div class="col-lg-4">
                <div class="feature-item right-align" data-aos="zoom-in" data-aos-delay="100">
                    <div class="icon-wrap"><i class="fas fa-stethoscope"></i></div>
                    <h3>Expert Medical Experience</h3>
                    <p>Years of hands-on clinical practice and specialized training.</p>
                </div>
                <div class="feature-item right-align" data-aos="zoom-in" data-aos-delay="200">
                    <div class="icon-wrap"><i class="fas fa-user-md"></i></div>
                    <h3>Patient-Centered Care</h3>
                    <p>Personalized treatment plans tailored to individual needs.</p>
                </div>
                <div class="feature-item right-align" data-aos="zoom-in" data-aos-delay="300">
                    <div class="icon-wrap"><i class="fas fa-heart"></i></div>
                    <h3>Compassion & Empathy</h3>
                    <p>Dedicated to listening and understanding every patient.</p>
                </div>
            </div>
            <div class="col-lg-4 text-center" data-aos="zoom-in">
                <div class="doctor-image-container"><img src="product_img/fea2.png" alt="Dr. Samidha Gore" class="img-fluid dr-hero-img"></div>
            </div>
            <div class="col-lg-4">
                <div class="feature-item" data-aos="zoom-in" data-aos-delay="100">
                    <div class="icon-wrap"><i class="fas fa-microscope"></i></div>
                    <h3>Accurate Diagnosis</h3>
                    <p>Using modern diagnostic tools and up-to-date medical knowledge.</p>
                </div>
                <div class="feature-item" data-aos="zoom-in" data-aos-delay="200">
                    <div class="icon-wrap"><i class="fas fa-calendar-check"></i></div>
                    <h3>Convenient Appointments</h3>
                    <p>Easy scheduling and dedicated post-care support.</p>
                </div>
                <div class="feature-item" data-aos="zoom-in" data-aos-delay="300">
                    <div class="icon-wrap"><i class="fas fa-lightbulb"></i></div>
                    <h3>Innovation</h3>
                    <p>Staying updated with the latest medical advancements.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="commodity-slider-section mb-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-right">
            <div>
                <span class="section-subtitle font-weight-bold">Our Commodities</span>
                <h2 class="section-title text-left" style="font-size: 38px;">
                    <?php echo isset($_SESSION['user_email']) ? "Restock Your Essentials" : "Trusted Nutritional Solutions"; ?>
                </h2>
            </div>
            <div class="slider-nav">
                <button class="prev-btn btn-nav"><i class="fas fa-chevron-left"></i></button>
                <button class="next-btn btn-nav"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="commodity-carousel">
            <div class="commodity-slide">
                <div class="product-display">
                    <img src="/Aquil/product_img/Progirl_protine_powder.webp" alt="PROgirl">
                </div>
                <h4>PROgirl</h4>
                <span>Adolescent Nutrition</span>
            </div>
            <div class="commodity-slide">
                <div class="product-display">
                    <img src="/Aquil/product_img/Prega_Mom_PL.webp" alt="Pregamom">
                </div>
                <h4>Pregamom Plus</h4>
                <span>Maternal Health</span>
            </div>
            <div class="commodity-slide">
                <div class="product-display">
                    <img src="/Aquil/product_img/Nutriplus_Mom.webp" alt="Nutriplus Mom">
                </div>
                <h4>Nutriplus Mom</h4>
                <span>Lactation Support</span>
            </div>
            <div class="commodity-slide">
                <div class="product-display">
                    <img src="/Aquil/product_img/Pulmino_Plus_TB_Care.webp" alt="Pulmino">
                </div>
                <h4>Pulmino Plus</h4>
                <span>Specialized Care</span>
            </div>
            <div class="commodity-slide">
                <div class="product-display">
                    <img src="/Aquil/product_img/PROTILITY_Little_Champs.webp" alt="Protility">
                </div>
                <h4>Protility</h4>
                <span>Little Champs</span>
            </div>
            <div class="commodity-slide">
                <div class="product-display">
                    <img src="/Aquil/product_img/Nutriplus_Junior.webp" alt="Nutriplus Junior">
                </div>
                <h4>Nutriplus Junior</h4>
                <span>Daily Wellness</span>
            </div>
        </div>
    </div>
</section>

<section class="reviews-section py-5" style="background: #f8f9fa; border-top: 1px solid #eee;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">What Our Members Say</h2>
        </div>

        <div class="row mb-5">
            <?php
            if (isset($conn) && $conn) {
                $query = "SELECT USER_NAME, COMMENT_TEXT, RATING FROM SYSTEM.AQUIL_REVIEWS ORDER BY CREATED_AT DESC FETCH FIRST 3 ROWS ONLY";
                $stid = oci_parse($conn, $query);
                oci_execute($stid);
                
                while ($row = oci_fetch_array($stid, OCI_ASSOC)):
                    $comment = $row['COMMENT_TEXT'];
                    if (is_object($comment)) { $comment = $comment->load(); }
            ?>
            <div class="col-md-4 mb-4" data-aos="fade-up">
                <div class="card h-100  shadow" style="border-radius: 20px; border-bottom: 5px solid #2a7f62;">
                    <div class="card-body p-4">
                        <div class="text-warning mb-3">
                            <?php echo str_repeat('⭐', $row['RATING']); ?>
                        </div>
                        <p class="text-muted italic">"<?php echo htmlspecialchars($comment); ?>"</p>
                        <div class="d-flex align-items-center mt-4">
                            <div style="width:40px; height:40px; background:#2a7f62; color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; margin-right:12px;">
                                <?php echo strtoupper(substr($row['USER_NAME'], 0, 1)); ?>
                            </div>
                            <h6 class="mb-0 font-weight-bold"><?php echo htmlspecialchars($row['USER_NAME']); ?></h6>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; } ?>
        </div>

        <?php if(isset($_SESSION['user_email'])): ?>
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg" style="border-radius: 25px; background: linear-gradient(145deg, #ffffff, #f0f4f3);">
                    <div class="card-body p-5">
                        <h4 class="font-weight-bold text-center mb-4">Post Your Review</h4>
                        <form action="process_review.php" method="POST">
                            <div class="form-group">
                                <label class="small font-weight-bold text-uppercase">Your Rating</label>
                                <select name="rating" class="form-control custom-select border-0 shadow-sm" style="height: 50px; border-radius: 10px;">
                                    <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                                    <option value="4">⭐⭐⭐⭐ (Very Good)</option>
                                    <option value="3">⭐⭐⭐ (Good)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-uppercase">Your Message</label>
                                <textarea name="comment" class="form-control border-0 shadow-sm" rows="4" placeholder="How was your experience with AquilLife?" style="border-radius: 15px;" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block py-3 shadow" style="border-radius: 10px; font-weight: bold; background: #2a7f62; border: none;">
                                SUBMIT FEEDBACK <i class="fas fa-paper-plane ml-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center">
            <p class="text-muted">Want to leave a review? <a href="login.php" class="text-primary font-weight-bold">Login here</a></p>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.commodity-carousel');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');

    if (carousel && prevBtn && nextBtn) {
        // How much to scroll (one slide width + gap)
        const getScrollAmount = () => {
            const slide = document.querySelector('.commodity-slide');
            return slide ? slide.clientWidth + 30 : 300; 
        };

        nextBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
        });

        prevBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
        });
    }
});
</script>
<?php include 'footer.php'; ?>



