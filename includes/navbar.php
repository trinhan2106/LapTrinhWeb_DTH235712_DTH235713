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
// Xác định trang hiện tại để đánh dấu "active" trên menu
$currentPage = $currentPage ?? '';

/**
 * Hàm trả về class "active" nếu $pageName trùng với $currentPage
 */
function isActive($pageName) {
    global $currentPage;
    return ($currentPage === $pageName) ? 'active fw-bold' : '';
}
?>

<nav class="navbar navbar-expand-lg sticky-top shadow-sm"
     style="background-color: #1a3c6e;">

    <div class="container">

        <!-- ── LOGO & TÊN THƯƠNG HIỆU ── -->
        <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="index.php">
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
                       href="index.php">
                        <i class="bi bi-house-door me-1"></i>Trang chủ
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white <?php echo isActive('phong_trong'); ?>"
                       href="phong_trong.php">
                        <i class="bi bi-door-open me-1"></i>Phòng trống
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white <?php echo isActive('gioi_thieu'); ?>"
                       href="gioi_thieu.php">
                        <i class="bi bi-info-circle me-1"></i>Giới thiệu
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white <?php echo isActive('lien_he'); ?>"
                       href="lien_he.php">
                        <i class="bi bi-telephone me-1"></i>Liên hệ
                    </a>
                </li>

            </ul>

            <!-- ── NÚT ĐĂNG KÝ THUÊ ── -->
            <div class="d-flex gap-2">
                <a href="dang_ky_thue.php" class="btn btn-warning btn-sm fw-semibold px-3">
                    <i class="bi bi-pencil-square me-1"></i>Đăng ký thuê
                </a>
                <a href="admin/dangnhap.php" class="btn btn-outline-light btn-sm px-3">
                    <i class="bi bi-shield-lock me-1"></i>Quản trị
                </a>
            </div>

        </div>
    </div>
</nav>