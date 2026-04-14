<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user_email'];
$fetchSql = "SELECT MOBILE, LOCATION, PROFILE_PIC FROM SYSTEM.AQUIL_ACCOUNTS WHERE EMAIL = :e";
$fetchStid = oci_parse($conn, $fetchSql);
oci_bind_by_name($fetchStid, ":e", $email);
oci_execute($fetchStid);
$currentUser = oci_fetch_array($fetchStid, OCI_ASSOC);
$currentMobile = $_SESSION['user_mobile'] ?? ($currentUser['MOBILE'] ?? '');
$currentLocation = $_SESSION['user_location'] ?? ($currentUser['LOCATION'] ?? '');
$currentPic = $_SESSION['profile_pic'] ?? ($currentUser['PROFILE_PIC'] ?? 'product_img/default_user.jpg');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mobile = $_POST['mobile'];
    $location = $_POST['location'];
    $profile_pic = $currentPic;

    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
        $upload_dir = "uploads/profile_pics/";
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
        $file_name = time() . "_" . basename($_FILES['profile_img']['name']);
        $file_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $file_path)) {
            $profile_pic = $file_path;
        }
    }

    $sql = "UPDATE SYSTEM.AQUIL_ACCOUNTS SET MOBILE=:m, LOCATION=:l, PROFILE_PIC=:p WHERE EMAIL=:e";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":m", $mobile);
    oci_bind_by_name($stid, ":l", $location);
    oci_bind_by_name($stid, ":p", $profile_pic);
    oci_bind_by_name($stid, ":e", $email);

    if (oci_execute($stid)) {
        $_SESSION['user_mobile'] = $mobile;
        $_SESSION['user_location'] = $location;
        $_SESSION['profile_pic'] = $profile_pic;
        $target = isset($_GET['redirect']) ? $_GET['redirect'] : 'user_dashboard.php?success=1';
        header("Location: " . $target);
        exit();
    }
}

include 'header.php';
include 'nav.php';
?>

<style>
    :root {
        --aquil-navy: #0a3d62;
        --aquil-green: #2a7f62;
        --soft-bg: #f8fafc;
    }

    body { background-color: var(--soft-bg); }

    .profile-card {
        border: none;
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
        overflow: hidden;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--aquil-navy), var(--aquil-green));
        color: white;
        padding: 30px;
        text-align: center;
    }

    .profile-img-preview {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-top: -45px;
        background: white;
    }

    .form-container { padding: 30px 50px 50px; }

    .aquil-input {
        border-radius: 12px;
        padding: 12px 18px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        font-weight: 500;
        transition: 0.3s;
    }

    .aquil-input:focus {
        border-color: var(--aquil-green);
        box-shadow: 0 0 0 4px rgba(42, 127, 98, 0.1);
        background: #fff;
    }

    .btn-save {
        background: var(--aquil-navy);
        color: white;
        border-radius: 12px;
        padding: 12px;
        font-weight: 700;
        border: none;
        width: 100%;
    }

    .btn-save:hover {
        background: var(--aquil-green);
        transform: translateY(-2px);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="profile-card">
                <div class="profile-header">
                    <h3 class="font-weight-bold mb-0">Edit Profile</h3>
                    <p class="small opacity-75 ">Update your personal information</p>
                </div>
                <div class="text-center">
                    <div class="profile-img-wrapper">
                        <?php 
                            $updatePic = $_SESSION['profile_pic'] ?? '';
                            if (empty($updatePic) || !file_exists($updatePic)) {
                                $updatePic = 'product_img/default_user.jpg';
                            }
                        ?>
                        <img src="<?php echo $updatePic; ?>" class="profile-img-preview" id="preview">
                    </div>
                </div>

                <div class="form-container">
                    <form action="update_profile.php<?php echo isset($_GET['redirect']) ? '?redirect='.$_GET['redirect'] : ''; ?>" method="POST" enctype="multipart/form-data">
                        
                        <div class=" text-center">
                            <label for="profile_img" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="cursor:pointer">
                                <i class="fas fa-camera mr-2"></i>Change Photo
                            </label>
                            <input type="file" name="profile_img" id="profile_img" class="d-none" onchange="previewImage(this)">
                        </div>

                        <div class="mb-3">
                            <label class="small font-weight-bold text-muted text-uppercase">Mobile Number</label>
                            <input class="form-control aquil-input" name="mobile" type="text" 
                                   value="<?php echo htmlspecialchars($currentMobile); ?>" 
                                   placeholder="Enter mobile number" required />
                        </div>

                        <div class="mb-4">
                            <label class="small font-weight-bold text-muted text-uppercase">Delivery Location</label>
                            <input class="form-control aquil-input" name="location" type="text" 
                                   value="<?php echo htmlspecialchars($currentLocation); ?>" 
                                   placeholder="City, State, Zip" required />
                        </div>

                        <button type="submit" class="btn btn-save shadow-sm mb-3">
                            Save Profile Details
                        </button>
                        
                        <div class="text-center">
                            <a class="small text-muted font-weight-bold" href="services.php">
                                <i class="fas fa-chevron-left mr-2"></i> Back to shop
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'footer.php'; ?>

