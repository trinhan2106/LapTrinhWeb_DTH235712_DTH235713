<?php
/**
 * NAVBAR.PHP – Editorial Edition
 * Custom navigation system using the Serif Design System
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Current page logic for Active state
$currentPage = $currentPage ?? '';

function isActive($pageName) {
    global $currentPage;
    return ($currentPage === $pageName) ? 'active' : '';
}

$quyenHan = $_SESSION['quyenHan'] ?? -1;
$tenHienThi = $_SESSION['tenNV'] ?? $_SESSION['tenKH'] ?? '';
?>

<!-- Clean Editorial Navbar -->
<nav class="navbar navbar-expand-lg sticky-top navbar-editorial">
    <div class="container-editorial d-flex align-items-center justify-content-between w-100">

        <!-- ── LOGO BRANDING ── -->
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php">
            CAOCENTER
        </a>

        <!-- ── MOBILE TOGGLE ── -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#editorialNavbar">
            <span class="bi bi-list fs-2"></span>
        </button>

        <!-- ── NAVIGATION LINKS ── -->
        <div class="collapse navbar-collapse" id="editorialNavbar">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('index'); ?>" href="<?php echo BASE_URL; ?>/index.php">
                        Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('phong_trong'); ?>" href="<?php echo BASE_URL; ?>/phong_trong.php">
                        Phòng trống
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('lien_he'); ?>" href="<?php echo BASE_URL; ?>/lien_he.php">
                        Liên hệ
                    </a>
                </li>
            </ul>

            <!-- ── AUTHENTICATION AREA ── -->
            <div class="d-flex align-items-center gap-3">
                <?php if ($quyenHan === -1): ?>
                    <a href="<?php echo BASE_URL; ?>/dangnhap.php" class="nav-link p-0 m-0" style="font-size: 0.85rem;">
                        ĐĂNG NHẬP
                    </a>
                    <a href="<?php echo BASE_URL; ?>/dang_ky_thue.php" class="btn-premium py-2 px-4 shadow-sm" style="font-size: 0.8rem;">
                        ĐĂNG KÝ
                    </a>

                <?php else: ?>
                    <span class="mono-label m-0" style="font-size: 0.75rem;">
                        Chào, <?php echo $tenHienThi; ?>
                    </span>
                    <a href="<?php echo BASE_URL; ?>/dangxuat.php" class="text-dark">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>