<?php
/**
 * DANG_KY_THUE.PHP – Form đăng ký thuê phòng (Trang Public – Khách)
 *
 * Mục đích:
 *   Khách hàng điền thông tin liên hệ và chọn phòng muốn thuê.
 *   Hệ thống lưu yêu cầu vào bảng YEU_CAU_THUE (nếu có) hoặc
 *   đơn giản gửi mail/thông báo cho Admin xử lý.
 *
 * Clean Code:
 *   - Chỉ chứa logic form + lấy danh sách phòng trống
 *   - Tất cả giao diện chung (header/navbar/footer) đều dùng include_once
 *
 * Lưu ý đồ án:
 *   Trang này KHÔNG yêu cầu đăng nhập (trang public).
 *   Xử lý submit → dang_ky_thue_submit.php (tách riêng theo quy tắc).
 */

// ── Kết nối CSDL ─────────────────────────────────────────────
require_once "cauhinh.php";
require_once "thuvien.php";

// ── Biến cho các module include ───────────────────────────────
$pageTitle   = "Đăng ký thuê phòng";
$currentPage = "dang_ky_thue";
$showSearch  = false; // Banner không hiện ô tìm kiếm trên trang này

// Tùy chỉnh banner cho trang đăng ký
$bannerTitle    = 'Đăng ký thuê <span class="text-warning">phòng văn phòng</span>';
$bannerSubtitle = 'Điền thông tin bên dưới, chúng tôi sẽ liên hệ tư vấn và xếp lịch tham quan trong vòng 24 giờ.';

// ── Nhận ?maPhong= từ URL (nếu khách click từ trang chủ) ─────
$maPhong_preselect = trim($_GET['maPhong'] ?? '');

// ── Thông báo từ lần submit trước ─────────────────────────────
$thong_bao = trim($_GET['thanhcong'] ?? '');
$loi_gui   = trim($_GET['loi']       ?? '');

// ── Load danh sách phòng TRỐNG để đổ vào dropdown ────────────
$sql_phong_trong = "SELECT
                        p.maPhong,
                        t.soTang,
                        c.tenCaoOc,
                        p.dienTich,
                        p.soChoLamViec,
                        ROUND(p.donGiaM2 * p.dienTich * t.heSoGia, 0) AS giaThue
                    FROM PHONG p
                    JOIN TANG   t ON p.maTang  = t.maTang
                    JOIN CAO_OC c ON t.maCaoOc = c.maCaoOc
                    WHERE p.trangThai = 'Trống'
                    ORDER BY c.tenCaoOc, t.soTang, p.maPhong";

$result_phong = $conn->query($sql_phong_trong);
?>

<?php include_once "includes/header.php"; ?>
<?php include_once "includes/navbar.php"; ?>
<?php include_once "includes/banner.php"; ?>

<!-- ══════════════════════════════════════════════════════════
     PHẦN THÂN – FORM ĐĂNG KÝ THUÊ PHÒNG
     ══════════════════════════════════════════════════════════ -->
<main class="container my-5">
<div class="row justify-content-center">

    <!-- Cột chứa form (8/12) -->
    <div class="col-lg-8">

        <h3 class="fw-bold mb-1">📝 Đăng ký thuê phòng văn phòng</h3>
        <p class="text-muted mb-4">
            Vui lòng điền đầy đủ thông tin bên dưới. Nhân viên tư vấn sẽ liên hệ
            xác nhận trong vòng <strong>24 giờ</strong>.
        </p>

        <!-- ── Thông báo thành công ── -->
        <?php if (!empty($thong_bao)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo e($thong_bao); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- ── Thông báo lỗi ── -->
        <?php if (!empty($loi_gui)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo e($loi_gui); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!--
            FORM ĐĂNG KÝ THUÊ
            method="POST" → xử lý tại dang_ky_thue_submit.php
            (Tách biệt giao diện vs xử lý – Clean Code)
        -->
        <form method="POST" action="dang_ky_thue_submit.php" novalidate
              class="p-4 bg-white border rounded-4 shadow-sm">

            <!-- ── PHẦN 1: THÔNG TIN KHÁCH HÀNG ── -->
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                <i class="bi bi-person-circle me-2"></i>1. Thông tin liên hệ
            </h5>

            <!-- Họ và tên -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Họ và tên <span class="text-danger">*</span>
                </label>
                <input type="text" name="hoTen" class="form-control"
                       placeholder="Nguyễn Văn A / Công ty TNHH ABC" required maxlength="100">
            </div>

            <!-- Số điện thoại + Email (2 cột) -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        Số điện thoại <span class="text-danger">*</span>
                    </label>
                    <input type="tel" name="soDienThoai" class="form-control"
                           placeholder="0909 123 456" required maxlength="15">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Email liên hệ</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="email@congty.vn" maxlength="100">
                </div>
            </div>

            <!-- Địa chỉ công ty -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Địa chỉ hiện tại</label>
                <input type="text" name="diaChi" class="form-control"
                       placeholder="123 Đường ABC, Quận X, TP.HCM" maxlength="200">
            </div>

            <!-- ── PHẦN 2: THÔNG TIN PHÒNG MUỐN THUÊ ── -->
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                <i class="bi bi-door-open me-2"></i>2. Phòng muốn thuê
            </h5>

            <!-- Chọn phòng từ danh sách trống -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Chọn phòng cụ thể <span class="text-danger">*</span>
                </label>
                <select name="maPhong" id="maPhong" class="form-select" required
                        onchange="capNhatThongTinPhong()">
                    <option value="">-- Chọn phòng muốn thuê --</option>
                    <?php
                    // Đổ danh sách phòng trống vào dropdown
                    // Pre-select nếu khách click "Đăng ký thuê" từ card cụ thể
                    if ($result_phong && $result_phong->num_rows > 0):
                        while ($phong = $result_phong->fetch_assoc()):
                            $selected = ($phong['maPhong'] === $maPhong_preselect) ? 'selected' : '';
                            $label    = sprintf(
                                '%s – %s Tầng %d (%s m², %s chỗ) – %s/tháng',
                                $phong['maPhong'],
                                $phong['tenCaoOc'],
                                $phong['soTang'],
                                number_format($phong['dienTich'], 1),
                                $phong['soChoLamViec'],
                                number_format($phong['giaThue'], 0, ',', '.')
                            );
                            printf(
                                '<option value="%s" %s data-gia="%s">%s</option>',
                                e($phong['maPhong']),
                                $selected,
                                $phong['giaThue'],
                                e($label)
                            );
                        endwhile;
                    else:
                        echo '<option value="" disabled>Hiện không có phòng trống</option>';
                    endif;
                    ?>
                </select>
                <div class="form-text">Chỉ hiển thị phòng đang có sẵn để thuê.</div>
            </div>

            <!-- Thông tin giá thuê (hiện lên khi chọn phòng) -->
            <div id="giaThueInfo" class="alert alert-info py-2 small" style="display:none;">
                <i class="bi bi-info-circle me-1"></i>
                Giá thuê phòng đã chọn: <strong id="giaThueHienThi" class="text-primary fs-6"></strong>/tháng.
                Kỳ đầu tiên thanh toán gộp <strong>6 tháng</strong>.
            </div>

            <!-- Thời gian muốn thuê -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        Dự kiến ngày bắt đầu <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="ngayBatDau" class="form-control" required
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        Thời gian thuê dự kiến (tháng) <span class="text-danger">*</span>
                    </label>
                    <select name="thoiGianThue" class="form-select" required>
                        <option value="">-- Chọn thời gian --</option>
                        <option value="6">6 tháng (tối thiểu)</option>
                        <option value="12">12 tháng (1 năm)</option>
                        <option value="18">18 tháng</option>
                        <option value="24">24 tháng (2 năm)</option>
                        <option value="36">36 tháng (3 năm)</option>
                    </select>
                    <div class="form-text">⚠️ Hệ thống yêu cầu thuê tối thiểu 6 tháng.</div>
                </div>
            </div>

            <!-- ── PHẦN 3: GHI CHÚ ── -->
            <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">
                <i class="bi bi-chat-text me-2"></i>3. Ghi chú thêm
            </h5>

            <div class="mb-4">
                <label class="form-label fw-semibold">Yêu cầu hoặc câu hỏi của bạn</label>
                <textarea name="ghiChu" class="form-control" rows="3"
                          placeholder="VD: Tôi cần phòng có view đẹp, gần thang máy, hoặc có nhu cầu đặc biệt khác..."></textarea>
            </div>

            <!-- ── CAM KẾT & SUBMIT ── -->
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="dongYDieuKhoan" required>
                <label class="form-check-label small" for="dongYDieuKhoan">
                    Tôi đã đọc và đồng ý với
                    <a href="#" class="text-primary">Điều khoản sử dụng</a>
                    và xác nhận thông tin tôi cung cấp là chính xác.
                    <span class="text-danger">*</span>
                </label>
            </div>

            <div class="d-grid gap-2 d-md-flex">
                <button type="submit" class="btn btn-gold px-5 py-2 fw-bold">
                    <i class="bi bi-send me-2"></i>Gửi yêu cầu đăng ký
                </button>
                <a href="index.php" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
            </div>

            <p class="text-muted small mt-3">
                <span class="text-danger">*</span> Các trường có dấu sao là bắt buộc.
                Nhân viên sẽ liên hệ trong vòng 24 giờ để xác nhận.
            </p>

        </form><!-- end form -->

    </div><!-- end col-lg-8 -->

    <!-- Cột thông tin bên phải (4/12) -->
    <div class="col-lg-4 mt-4 mt-lg-0">

        <!-- Card liên hệ hotline -->
        <div class="card border-0 bg-primary text-white rounded-4 mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">📞 Tư vấn trực tiếp</h6>
                <div class="fs-4 fw-bold text-warning mb-1">0909 123 456</div>
                <p class="small mb-0 opacity-75">Thứ 2 – Thứ 7: 8:00 – 18:00</p>
            </div>
        </div>

        <!-- Quy trình thuê phòng -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-dark">
                    <i class="bi bi-list-ol me-2 text-primary"></i>Quy trình thuê phòng
                </h6>
                <div class="d-flex gap-3 mb-3">
                    <span class="badge bg-primary rounded-circle" style="width:28px;height:28px;line-height:20px;">1</span>
                    <div class="small">
                        <div class="fw-semibold">Đăng ký yêu cầu</div>
                        <div class="text-muted">Điền form, chọn phòng phù hợp</div>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <span class="badge bg-primary rounded-circle" style="width:28px;height:28px;line-height:20px;">2</span>
                    <div class="small">
                        <div class="fw-semibold">Nhân viên liên hệ</div>
                        <div class="text-muted">Xác nhận thông tin trong 24h</div>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <span class="badge bg-primary rounded-circle" style="width:28px;height:28px;line-height:20px;">3</span>
                    <div class="small">
                        <div class="fw-semibold">Tham quan phòng</div>
                        <div class="text-muted">Xem thực tế, xác nhận giá thuê</div>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <span class="badge bg-success rounded-circle" style="width:28px;height:28px;line-height:20px;">4</span>
                    <div class="small">
                        <div class="fw-semibold">Ký hợp đồng</div>
                        <div class="text-muted">Thanh toán kỳ đầu, nhận chìa khóa</div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- end col-lg-4 -->

</div><!-- end row -->
</main>

<?php
$conn->close();
include_once "includes/footer.php";
?>

<!-- Script: Hiển thị giá thuê khi chọn phòng -->
<script>
function capNhatThongTinPhong() {
    var sel = document.getElementById('maPhong');
    var opt = sel.options[sel.selectedIndex];
    var gia = opt ? parseInt(opt.getAttribute('data-gia') || 0) : 0;

    var infoBox    = document.getElementById('giaThueInfo');
    var giaHienThi = document.getElementById('giaThueHienThi');

    if (gia > 0) {
        // Định dạng số theo kiểu Việt Nam
        giaHienThi.textContent = gia.toLocaleString('vi-VN') + ' ₫';
        infoBox.style.display = 'block';
    } else {
        infoBox.style.display = 'none';
    }
}

// Chạy ngay khi tải trang (trường hợp ?maPhong= đã được pre-select)
window.addEventListener('DOMContentLoaded', capNhatThongTinPhong);
</script>