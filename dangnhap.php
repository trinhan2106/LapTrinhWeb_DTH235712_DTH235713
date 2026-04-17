<?php
/**
 * DANGNHAP.PHP – Editorial Login Portal
 */
require_once "cauhinh.php";
require_once "thuvien.php";

session_start();
if (isset($_SESSION['maNV'])) {
    header("Location: " . BASE_URL . "/admin/index.php");
    exit();
}
if (isset($_SESSION['maKH'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}

$pageTitle = "Đăng nhập Cổng Portal";
$loi = $_GET['loi'] ?? '';

include_once "includes/header.php";
include_once "includes/navbar.php";
?>

<main class="section-editorial container-editorial d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="card-phong-editorial w-100" style="max-width: 450px;">
        
        <div class="text-center mb-5">
            <span class="mono-label">Authentication Portal</span>
            <h2 class="serif-heading mt-2">ĐĂNG NHẬP HỆ THỐNG</h2>
            <div class="rule-premium" style="width: 50px; margin: 1.5rem auto;"></div>
        </div>

        <?php if ($loi): ?>
            <div class="alert alert-danger rounded-0 small border-0 mb-4" style="background-color: rgba(220, 53, 69, 0.1); color: #842029;">
                <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($loi); ?>
            </div>
        <?php endif; ?>

        <form action="dangnhap_submit.php" method="POST">
            <div class="mb-4">
                <label class="form-premium-label">Tên đăng nhập</label>
                <input type="text" name="tenDangNhap" class="form-premium-control" placeholder="Nhập tên đăng nhập của bạn" required autofocus>
            </div>
            
            <div class="mb-5">
                <label class="form-premium-label">Mật khẩu</label>
                <input type="password" name="matKhau" class="form-premium-control" placeholder="••••••••" required>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn-premium py-3">
                    XÁC NHẬN ĐĂNG NHẬP <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </form>
        
        <div class="text-center mt-5 pt-3">
            <a href="<?php echo BASE_URL; ?>/index.php" class="text-dark text-decoration-none small" style="opacity: 0.6;">
                <i class="bi bi-house me-1"></i> Quay lại trang chủ
            </a>
        </div>
    </div>
</main>

<?php include_once "includes/footer.php"; ?>
