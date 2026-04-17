<?php
/**
 * DANGNHAP.PHP – Trang đăng nhập hệ thống hợp nhất (Đã chuyển ra Root)
 */
require_once "cauhinh.php";
require_once "thuvien.php";

// Nếu đã đăng nhập rồi thì điều hướng về đúng khu vực
session_start();
if (isset($_SESSION['maNV'])) {
    header("Location: " . BASE_URL . "/admin/index.php");
    exit();
}
if (isset($_SESSION['maKH'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}

$loi = $_GET['loi'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống | Office building</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { width: 100%; max-width: 400px; padding: 2.5rem; border-radius: 1.5rem; background: #fff; box-shadow: 0 15px 35px rgba(0,0,0,0.07); }
        .btn-primary { background: #1a3c6e; border: none; padding: 0.75rem; font-weight: 600; }
        .btn-outline-secondary { padding: 0.75rem; font-weight: 600; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-primary">PORTAL LOGIN</h3>
        <p class="text-muted small">Cổng truy cập Nhân viên & Khách hàng</p>
    </div>

    <?php if ($loi): ?>
        <div class="alert alert-danger small py-2"><?php echo htmlspecialchars($loi); ?></div>
    <?php endif; ?>

    <form action="dangnhap_submit.php" method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold">Tên đăng nhập</label>
            <input type="text" name="tenDangNhap" class="form-control" placeholder="Tên đăng nhập" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Mật khẩu</label>
            <input type="password" name="matKhau" class="form-control" placeholder="Mật khẩu" required>
        </div>
        
        <div class="row g-2">
            <div class="col-8">
                <button type="submit" class="btn btn-primary w-100">Đăng Nhập</button>
            </div>
            <div class="col-4">
                <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-outline-secondary w-100">
                    Trang chủ
                </a>
            </div>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p class="small text-muted mb-0">Hệ thống quản lý vận hành cao ốc v1.0</p>
    </div>
</div>

</body>
</html>
