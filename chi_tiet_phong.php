<?php
/**
 * CHI_TIET_PHONG.PHP – Xem chi tiết một phòng cụ thể (Trang Public)
 *
 * Nhận tham số: ?id=maPhong (VD: ?id=P-00001)
 *
 * Luồng:
 *   1. Nhận và làm sạch tham số `id` từ URL
 *   2. Query thông tin phòng JOIN TANG, CAO_OC
 *   3. Nếu không tìm thấy → redirect về phong_trong.php kèm thông báo
 *   4. Hiển thị đầy đủ thông tin phòng
 *   5. Nút "Đăng ký thuê ngay" link sang dang_ky_thue.php?maPhong=...
 */

// ── Kết nối CSDL ──────────────────────────────────────────────
require_once "cauhinh.php";
require_once "thuvien.php";

// ── Nhận và làm sạch tham số URL ──────────────────────────────
// real_escape_string() để chống SQL Injection
$maPhong = trim($_GET['id'] ?? '');
$maPhong = $conn->real_escape_string($maPhong);

// ── Kiểm tra tham số hợp lệ ───────────────────────────────────
if (empty($maPhong)) {
    // Không có id → redirect về danh sách phòng
    header("Location: phong_trong.php?loi=" . urlencode("Không tìm thấy mã phòng yêu cầu!"));
    exit();
}

// ── Query chi tiết phòng (JOIN lấy thông tin tầng và cao ốc) ──
$sql = "SELECT
            p.maPhong,
            p.dienTich,
            p.soChoLamViec,
            p.moTaViTri,
            p.donGiaM2,
            p.trangThai,
            t.soTang,
            t.heSoGia,
            c.maCaoOc,
            c.tenCaoOc,
            c.diaChi,
            c.moTa         AS moTaCaoOc,
            ROUND(p.donGiaM2 * p.dienTich * t.heSoGia, 0)       AS giaThue,
            ROUND(p.donGiaM2 * p.dienTich * t.heSoGia * 6, 0)   AS giaKyDau
        FROM PHONG p
        JOIN TANG   t ON p.maTang  = t.maTang
        JOIN CAO_OC c ON t.maCaoOc = c.maCaoOc
        WHERE p.maPhong = '$maPhong'
        LIMIT 1";

$result = $conn->query($sql);

// ── Kiểm tra phòng có tồn tại không ───────────────────────────
if (!$result || $result->num_rows === 0) {
    header("Location: phong_trong.php?loi=" . urlencode("Phòng '$maPhong' không tồn tại trong hệ thống!"));
    exit();
}

$phong = $result->fetch_assoc(); // Lấy dữ liệu phòng

// ── Biến cho module header/navbar ─────────────────────────────
$pageTitle      = "Chi tiết phòng " . $phong['maPhong'];
$currentPage    = "phong_trong"; // Đánh dấu menu "Phòng trống" active
$showSearch     = false;
$bannerTitle    = 'Chi tiết <span class="text-warning">phòng ' . e($phong['maPhong']) . '</span>';
$bannerSubtitle = e($phong['tenCaoOc']) . ' – Tầng ' . $phong['soTang'] . ' – ' . e($phong['diaChi']);
?>

<?php include_once "includes/header.php"; ?>
<?php include_once "includes/navbar.php"; ?>
<?php include_once "includes/banner.php"; ?>

<!-- ══════════════════════════════════════════════════════════
     PHẦN THÂN – CHI TIẾT PHÒNG
     ══════════════════════════════════════════════════════════ -->
<main class="container my-5">

    <!-- ── BREADCRUMB điều hướng ── -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="phong_trong.php">Phòng trống</a></li>
            <li class="breadcrumb-item active"><?php echo e($phong['maPhong']); ?></li>
        </ol>
    </nav>

    <div class="row g-4">

        <!-- ── CỘT TRÁI: Thông tin chi tiết (8/12) ── -->
        <div class="col-lg-8">

            <!-- Card: Ảnh đại diện phòng (placeholder) -->
            <div class="rounded-4 overflow-hidden mb-4 shadow-sm"
                 style="height: 300px; background: linear-gradient(135deg, #e8f0fe 0%, #c3d3f0 100%);">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <i class="bi bi-building" style="font-size:7rem; color:#4a7cc7; opacity:.3;"></i>
                </div>
            </div>

            <!-- Card: Thông tin cơ bản -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="fw-bold text-primary mb-1">
                                Phòng <?php echo e($phong['maPhong']); ?>
                            </h3>
                            <p class="text-muted mb-0">
                                <i class="bi bi-building me-1"></i>
                                <?php echo e($phong['tenCaoOc']); ?>
                                – Tầng <?php echo e($phong['soTang']); ?>
                            </p>
                        </div>
                        <!-- Badge trạng thái -->
                        <?php echo badgeTrangThaiPhong($phong['trangThai']); ?>
                    </div>

                    <hr>

                    <!-- Thông số kỹ thuật (dạng icon + text) -->
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <i class="bi bi-aspect-ratio fs-3 text-primary d-block mb-1"></i>
                                <div class="fw-bold"><?php echo number_format($phong['dienTich'], 1); ?> m²</div>
                                <div class="text-muted small">Diện tích</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <i class="bi bi-people fs-3 text-success d-block mb-1"></i>
                                <div class="fw-bold"><?php echo e($phong['soChoLamViec']); ?></div>
                                <div class="text-muted small">Số chỗ làm việc</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <i class="bi bi-layers fs-3 text-warning d-block mb-1"></i>
                                <div class="fw-bold">Tầng <?php echo e($phong['soTang']); ?></div>
                                <div class="text-muted small">Vị trí tầng</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <i class="bi bi-graph-up fs-3 text-info d-block mb-1"></i>
                                <div class="fw-bold"><?php echo $phong['heSoGia']; ?>x</div>
                                <div class="text-muted small">Hệ số tầng</div>
                            </div>
                        </div>
                    </div>

                    <!-- Địa chỉ -->
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <i class="bi bi-geo-alt-fill text-danger mt-1"></i>
                        <div>
                            <div class="fw-semibold small text-muted">Địa chỉ</div>
                            <div><?php echo e($phong['diaChi']); ?></div>
                        </div>
                    </div>

                    <!-- Mô tả vị trí -->
                    <?php if (!empty($phong['moTaViTri'])): ?>
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <i class="bi bi-pin-map-fill text-primary mt-1"></i>
                        <div>
                            <div class="fw-semibold small text-muted">Mô tả vị trí</div>
                            <div><?php echo e($phong['moTaViTri']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Đơn giá/m² -->
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-cash-stack text-success mt-1"></i>
                        <div>
                            <div class="fw-semibold small text-muted">Đơn giá thuê</div>
                            <div><?php echo dinhDangTien($phong['donGiaM2']); ?>/m²/tháng</div>
                        </div>
                    </div>

                </div>
            </div><!-- end card thông tin -->

            <!-- Card: Giới thiệu cao ốc -->
            <?php if (!empty($phong['moTaCaoOc'])): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-building me-2 text-primary"></i>
                        Về <?php echo e($phong['tenCaoOc']); ?>
                    </h6>
                    <p class="text-muted mb-0" style="line-height:1.8;">
                        <?php echo e($phong['moTaCaoOc']); ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>

        </div><!-- end col-lg-8 -->

        <!-- ── CỘT PHẢI: Giá thuê + CTA (4/12) ── -->
        <div class="col-lg-4">

            <!-- Card giá thuê & nút đăng ký (sticky) -->
            <div class="card border-0 shadow rounded-4 sticky-top" style="top: 80px;">
                <div class="card-body p-4">

                    <!-- Giá thuê hàng tháng -->
                    <div class="text-center mb-4">
                        <div class="text-muted small mb-1">Giá thuê hàng tháng</div>
                        <div style="font-size:2rem; font-weight:800; color:#1a3c6e;">
                            <?php echo dinhDangTien($phong['giaThue']); ?>
                        </div>
                        <div class="text-muted small">/tháng</div>
                    </div>

                    <hr>

                    <!-- Bảng chi phí kỳ đầu -->
                    <table class="table table-sm table-borderless mb-4">
                        <tr>
                            <td class="text-muted small">Giá/m²:</td>
                            <td class="text-end small fw-semibold">
                                <?php echo dinhDangTien($phong['donGiaM2']); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Diện tích:</td>
                            <td class="text-end small fw-semibold">
                                <?php echo number_format($phong['dienTich'], 1); ?> m²
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Hệ số tầng:</td>
                            <td class="text-end small fw-semibold"><?php echo $phong['heSoGia']; ?>x</td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-bold text-danger small">Kỳ đầu (×6 tháng):</td>
                            <td class="text-end fw-bold text-danger small">
                                <?php echo dinhDangTien($phong['giaKyDau']); ?>
                            </td>
                        </tr>
                    </table>

                    <!-- Nút đăng ký thuê -->
                    <?php if ($phong['trangThai'] === 'Trống'): ?>
                    <div class="d-grid gap-2">
                        <a href="dang_ky_thue.php?maPhong=<?php echo urlencode($phong['maPhong']); ?>"
                           class="btn btn-gold py-2 fw-bold">
                            <i class="bi bi-pencil-square me-2"></i>Đăng ký thuê ngay
                        </a>
                        <a href="tel:09091234456" class="btn btn-outline-primary py-2">
                            <i class="bi bi-telephone me-2"></i>Gọi tư vấn miễn phí
                        </a>
                    </div>
                    <?php else: ?>
                    <!-- Phòng không còn trống -->
                    <div class="alert alert-warning text-center py-2 mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Phòng hiện không còn trống.
                    </div>
                    <a href="phong_trong.php" class="btn btn-outline-primary w-100">
                        Xem phòng trống khác
                    </a>
                    <?php endif; ?>

                    <hr class="my-3">

                    <!-- Lưu ý -->
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-1"></i> Thuê tối thiểu 6 tháng</li>
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-1"></i> Nhân viên liên hệ trong 24h</li>
                        <li class="mb-1"><i class="bi bi-check-circle text-success me-1"></i> Tham quan miễn phí</li>
                    </ul>

                </div>
            </div><!-- end card sticky -->

        </div><!-- end col-lg-4 -->

    </div><!-- end row -->

    <!-- Nút quay lại -->
    <div class="mt-4">
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>

</main><!-- end main -->

<?php
$conn->close();
include_once "includes/footer.php";
?>
