<!--
    NAVBAR.PHP – Thanh điều hướng (Navigation Bar) dùng chung cho trang Public
    Gọi bằng: include_once "includes/navbar.php";

    Tự động đánh dấu menu "active" dựa trên tên file đang chạy ($currentPage).
    Khai báo $currentPage TRƯỚC khi include:
        $currentPage = "index";        // Trang chủ
        $currentPage = "phong_trong";  // Danh sách phòng
        $currentPage = "lien_he";      // Liên hệ
        $currentPage = "dang_ky_thue"; // Đăng ký thuê
-->
<?php
// Bắt đầu session nếu chưa có để lấy thông tin đăng nhập
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xác định trang hiện tại để đánh dấu "active" trên menu
$currentPage = $currentPage ?? '';

/**
 * Hàm trả về class "active" nếu $pageName trùng với $currentPage
 */
function isActive($pageName) {
    global $currentPage;
    return ($currentPage === $pageName) ? 'active fw-bold' : '';
}

// Lấy thông tin session
$quyenHan = $_SESSION['quyenHan'] ?? -1; // -1 nghĩa là chưa đăng nhập
$tenHienThi = $_SESSION['tenNV'] ?? $_SESSION['tenKH'] ?? '';
?>

<nav class="navbar navbar-expand-lg sticky-top shadow-sm"
     style="background-color: #1a3c6e;">

    <div class="container">

        <!-- ── LOGO & TÊN THƯƠNG HIỆU ── -->
        <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="<?php echo BASE_URL; ?>/index.php">
            <span style="font-size:1.6rem;">🏢</span>
            <div class="lh-1">
                <div class="fw-bold fs-5">CAOCENTER</div>
                <div class="text-warning" style="font-size:.7rem; letter-spacing:1px;">
                    CHO THUÊ VĂN PHÒNG CAO ỐC
                </div>
            </div>
        </a>

        <!-- ── NÚT HAMBURGER (mobile) ── -->
        <button class="navbar-toggler border-secondary" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- ── MENU CÁC TRANG ── -->
        <div class="collapse navbar-collapse" id="mainNav">

            <ul class="navbar-nav mx-auto gap-1">

                <li class="nav-item">
                    <a class="nav-link text-white <?php echo isActive('index'); ?>"
                       href="<?php echo BASE_URL; ?>/index.php">
                        <i class="bi bi-house-door me-1"></i>Trang chủ
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white <?php echo isActive('phong_trong'); ?>"
                       href="<?php echo BASE_URL; ?>/phong_trong.php">
                        <i class="bi bi-door-open me-1"></i>Phòng trống
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white <?php echo isActive('gioi_thieu'); ?>"
                       href="<?php echo BASE_URL; ?>/gioi_thieu.php">
                        <i class="bi bi-info-circle me-1"></i>Giới thiệu
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white <?php echo isActive('lien_he'); ?>"
                       href="<?php echo BASE_URL; ?>/lien_he.php">
                        <i class="bi bi-telephone me-1"></i>Liên hệ
                    </a>
                </li>

            </ul>

            <!-- ── KHU VỰC ĐĂNG NHẬP / THÀNH VIÊN ── -->
            <div class="d-flex align-items-center gap-2">
                <?php if ($quyenHan === -1): ?>
                    <!-- CHƯA ĐĂNG NHẬP -->
                    <a href="<?php echo BASE_URL; ?>/dang_ky_thue.php" class="btn btn-warning btn-sm fw-semibold px-3">
                        <i class="bi bi-pencil-square me-1"></i>Đăng ký thuê
                    </a>
                    <a href="<?php echo BASE_URL; ?>/dangnhap.php" class="btn btn-outline-light btn-sm px-3">
                        <i class="bi bi-person-circle me-1"></i>Đăng nhập
                    </a>

                <?php elseif ($quyenHan > 0): ?>
                    <!-- LÀ NHÂN VIÊN (ADMIN/QUẢN LÝ) -->
                    <span class="text-white small me-2">
                        👋 Xin chào Nhân viên: <strong><?php echo e($tenHienThi); ?></strong>
                    </span>
                    <a href="<?php echo BASE_URL; ?>/admin/cao_oc/cao_oc_hienthi.php" class="btn btn-primary btn-sm px-3 border-light">
                        <i class="bi bi-speedometer2 me-1"></i>Vào Quản trị
                    </a>
                    <a href="<?php echo BASE_URL; ?>/dangxuat.php" class="btn btn-outline-danger btn-sm px-2 border-danger-subtle bg-danger bg-opacity-10 text-white">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>

                <?php else: ?>
                    <!-- LÀ KHÁCH HÀNG (quyenHan == 0) -->
                    <span class="text-white small me-2">
                        👋 Xin chào: <strong><?php echo e($tenHienThi); ?></strong>
                    </span>
                    <a href="<?php echo BASE_URL; ?>/khach_hang/my_contracts.php" class="btn btn-success btn-sm px-3">
                        <i class="bi bi-file-earmark-text me-1"></i>Hợp đồng của tôi
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/dangxuat.php" class="btn btn-outline-danger btn-sm px-2 border-danger-subtle bg-danger bg-opacity-10 text-white">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>