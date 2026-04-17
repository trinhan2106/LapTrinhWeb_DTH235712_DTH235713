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

<!-- ── HERO BANNER ────────────────────────────────────────── -->
<section
    class="py-5 text-white position-relative overflow-hidden"
    style="background: linear-gradient(135deg, #1a3c6e 0%, #2a5298 50%, #1a3c6e 100%);
           min-height: 260px;">

    <!-- Vòng trang trí nền (decorative circles) -->
    <div class="position-absolute top-0 end-0 opacity-10"
         style="width:400px; height:400px; border-radius:50%;
                background: radial-gradient(circle, #ffffff 0%, transparent 70%);
                transform: translate(100px, -150px);"></div>
    <div class="position-absolute bottom-0 start-0 opacity-10"
         style="width:300px; height:300px; border-radius:50%;
                background: radial-gradient(circle, #e8a020 0%, transparent 70%);
                transform: translate(-100px, 100px);"></div>

    <div class="container position-relative">
        <div class="row align-items-center">

            <!-- Nội dung banner bên trái -->
            <div class="col-lg-7 py-3">

                <!-- Tiêu đề chính -->
                <h1 class="fw-bold mb-3" style="font-size: clamp(1.6rem, 4vw, 2.5rem); line-height:1.3;">
                    <?php
                    /*
                     * $bannerTitle được render trực tiếp (KHÔNG escape)
                     * vì nó chứa HTML như <span class="text-warning">
                     * → Chỉ dùng với dữ liệu do DEVELOPER khai báo, KHÔNG phải từ người dùng nhập
                     */
                    echo $bannerTitle;
                    ?>
                </h1>

                <!-- Phụ đề -->
                <p class="mb-4 opacity-85"
                   style="font-size:1.05rem; max-width:520px; line-height:1.7; color:#c8d8f0;">
                    <?php echo e($bannerSubtitle); ?>
                </p>

                <!-- Nhóm nút CTA -->
                <div class="d-flex flex-wrap gap-2">
                    <a href="phong_trong.php"
                       class="btn btn-warning fw-semibold px-4 py-2">
                        <i class="bi bi-door-open me-1"></i>Xem phòng trống
                    </a>
                    <a href="dang_ky_thue.php"
                       class="btn btn-outline-light px-4 py-2">
                        <i class="bi bi-pencil-square me-1"></i>Đăng ký thuê
                    </a>
                </div>

            </div><!-- end col banner left -->

            <!-- Phần bên phải: Ô tìm kiếm nhanh (chỉ hiện nếu $showSearch = true) -->
            <?php if ($showSearch): ?>
            <div class="col-lg-5 mt-4 mt-lg-0">
                <div class="bg-white rounded-4 p-4 shadow-lg">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-search me-2 text-primary"></i>Tìm phòng nhanh
                    </h6>
                    <!--
                        Form tìm kiếm nhanh trong banner.
                        Submit GET về index.php để lọc danh sách phòng.
                    -->
                    <form method="GET" action="index.php">

                        <!-- Tìm theo từ khóa -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">
                                Từ khóa (mã phòng, tên cao ốc)
                            </label>
                            <input type="text" name="timkiem" class="form-control form-control-sm"
                                   placeholder="VD: P-001, Vinhomes...">
                        </div>

                        <!-- Lọc diện tích tối thiểu -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">
                                Diện tích tối thiểu
                            </label>
                            <select name="dienTich_min" class="form-select form-select-sm">
                                <option value="">Không giới hạn</option>
                                <?php
                                // Danh sách diện tích được hardcode vì đây là tùy chọn lọc UI
                                // (không phải dữ liệu nghiệp vụ từ DB)
                                foreach ([30 => '30 m²', 50 => '50 m²', 80 => '80 m²',
                                          100 => '100 m²', 150 => '150 m² trở lên'] as $val => $lbl) {
                                    printf('<option value="%d">%s</option>', $val, $lbl);
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Lọc số chỗ làm việc -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">
                                Số chỗ làm việc tối thiểu
                            </label>
                            <select name="socho_min" class="form-select form-select-sm">
                                <option value="">Không giới hạn</option>
                                <?php
                                foreach ([5 => '5+ chỗ', 10 => '10+ chỗ', 20 => '20+ chỗ', 50 => '50+ chỗ'] as $val => $lbl) {
                                    printf('<option value="%d">%s</option>', $val, $lbl);
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-search me-1"></i>Tìm kiếm phòng
                        </button>

                    </form>
                </div>
            </div><!-- end col search -->
            <?php endif; ?>

        </div><!-- end row -->
    </div><!-- end container -->

</section><!-- end hero banner -->
