<!--
    BANNER.PHP – Hero Section dùng chung cho các trang Public
    Gọi bằng: include_once "includes/banner.php";

    Biến tùy chỉnh (khai báo TRƯỚC khi include):
        $bannerTitle    = "Tiêu đề banner";   // Hỗ trợ HTML (ví dụ: <span class="text-warning">...)
        $bannerSubtitle = "Mô tả phụ";        // Text thuần, sẽ được escape
        $showSearch     = true;                // true = hiện form tìm kiếm nhanh inline

    Mặc định nếu không khai báo:
        $bannerTitle    = "Không gian làm việc đẳng cấp"
        $bannerSubtitle = "Hệ thống quản lý và cho thuê văn phòng cao ốc chuyên nghiệp"
        $showSearch     = false
-->
<?php
// ── Giá trị mặc định nếu file gọi không khai báo biến ───────
$bannerTitle    = $bannerTitle    ?? 'Không gian làm việc <span class="text-warning">đẳng cấp</span>';
$bannerSubtitle = $bannerSubtitle ?? 'Hệ thống quản lý và cho thuê văn phòng cao ốc chuyên nghiệp tại các vị trí đắc địa.';
$showSearch     = $showSearch     ?? false;
?>

<!-- ── HERO BANNER (Editorial Edition) ────────────────────────── -->
<section class="position-relative overflow-hidden" 
         style="background-color: var(--text-black); min-height: 480px; display: flex; align-items: center;">

    <!-- Background Image with Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background: linear-gradient(90deg, var(--text-black) 30%, rgba(26, 26, 26, 0.4) 100%), 
                     url('<?php echo BASE_URL; ?>/assets/images/hero-banner.png');
                background-size: cover;
                background-position: center;
                opacity: 0.85;"></div>

    <!-- Subtle Gold Accent Glow -->
    <div class="position-absolute bottom-0 start-0 w-100" 
         style="height: 1px; background: linear-gradient(90deg, transparent, var(--accent-gold), transparent); opacity: 0.3;"></div>

    <div class="container position-relative py-5">
        <div class="row align-items-center">

            <!-- Nội dung banner bên trái -->
            <div class="col-lg-7 py-3">
                <div class="text-ivory">
                    <span class="mono-label mb-3 d-block" style="color: var(--accent-gold); letter-spacing: 0.2em;">PREMIUM RESIDENCE</span>
                    <h1 class="serif-heading mb-4" style="font-size: clamp(2rem, 5vw, 3.5rem); line-height: 1.1; color: var(--bg-ivory);">
                        <?php echo $bannerTitle; ?>
                    </h1>
                    <p class="mb-5" style="font-size: 1.15rem; max-width: 550px; line-height: 1.8; color: rgba(250, 250, 248, 0.75);">
                        <?php echo e($bannerSubtitle); ?>
                    </p>

                    <!-- Nhóm nút CTA -->
                    <div class="d-flex flex-wrap gap-3">
                        <a href="phong_trong.php" class="btn-premium-gold px-5 py-3 shadow-lg">
                            <i class="bi bi-door-open me-2"></i>Xem phòng trống
                        </a>
                        <a href="dang_ky_thue.php" class="btn-premium-outline-white px-5 py-3">
                            <i class="bi bi-pencil-square me-2"></i>Đăng ký thuê
                        </a>
                    </div>
                </div>
            </div><!-- end col left -->

            <!-- Phần bên phải: Ô tìm kiếm nhanh -->
            <?php if ($showSearch): ?>
            <div class="col-lg-5 mt-5 mt-lg-0">
                <div class="bg-white p-5 shadow-2xl border-0" style="backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="mb-4">
                        <h4 class="serif-heading mb-1" style="font-size: 1.5rem;">TÌM KIẾM NHANH</h4>
                        <div class="rule-premium" style="width: 40px; margin: 0;"></div>
                    </div>
                    
                    <form method="GET" action="index.php">
                        <div class="mb-4">
                            <label class="form-premium-label">Từ khóa search</label>
                            <input type="text" name="timkiem" class="form-premium-control" placeholder="Mã phòng, tên tòa nhà...">
                        </div>

                        <div class="mb-4">
                            <label class="form-premium-label">Diện tích min</label>
                            <select name="dienTich_min" class="form-premium-control">
                                <option value="">Không giới hạn</option>
                                <?php
                                foreach ([30 => '30 m²', 50 => '50 m²', 80 => '80 m²', 150 => '150 m²+'] as $val => $lbl) {
                                    printf('<option value="%d">%s</option>', $val, $lbl);
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-5">
                            <label class="form-premium-label">Số chỗ ngồi</label>
                            <select name="socho_min" class="form-premium-control">
                                <option value="">Tất cả quy mô</option>
                                <?php
                                foreach ([5 => '5+ chỗ', 10 => '10+ chỗ', 20 => '20+ chỗ', 50 => '50+ chỗ'] as $val => $lbl) {
                                    printf('<option value="%d">%s</option>', $val, $lbl);
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn-premium w-100 py-3">
                            <i class="bi bi-search me-2"></i> BẮT ĐẦU TÌM KIẾM
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div><!-- end row -->
    </div><!-- end container -->
</section>
