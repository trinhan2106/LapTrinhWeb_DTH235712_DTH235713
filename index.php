<?php
/**
 * INDEX.PHP – Trang chủ (Public – Khách xem)
 *
 * Kiến trúc Clean / Modular:
 *   - Không chứa HTML <head> hay navbar inline
 *   - Dùng include_once để gọi các module từ /includes/
 *   - Phần thân (body) CHỈ chứa logic truy vấn DB và hiển thị danh sách phòng trống
 *
 * Luồng chạy:
 *   1. Kết nối CSDL qua cauhinh.php
 *   2. Truy vấn danh sách phòng đang TRỐNG (có lọc tùy chọn từ URL)
 *   3. Gọi module header → navbar → banner
 *   4. Hiển thị danh sách phòng dạng Card Bootstrap
 *   5. Gọi module footer
 */

// ── Bước 1: Kết nối CSDL và load hàm thư viện ────────────────
require_once "cauhinh.php";   // Khởi tạo $conn
require_once "thuvien.php";   // Các hàm: e(), dinhDangTien(), tinhGiaThue()...

// ── Bước 2: Khai báo biến trước khi include module ───────────
$pageTitle   = "Trang chủ";         // Tiêu đề tab – dùng trong header.php
$currentPage = "index";             // Đánh dấu menu active – dùng trong navbar.php
$showSearch  = true;                // Hiện ô tìm kiếm nhanh trong banner.php

// ── Bước 3: Nhận tham số lọc từ URL (nếu khách tự lọc) ──────
$loc_dienTich_min = (int)(isset($_GET['dienTich_min']) ? $_GET['dienTich_min'] : 0);
$loc_socho_min    = (int)(isset($_GET['socho_min']) ? $_GET['socho_min'] : 0);
$loc_timkiem      = trim(isset($_GET['timkiem']) ? $_GET['timkiem'] : '');

// ── Bước 4: Xây dựng SQL lấy danh sách phòng TRỐNG ──────────
$sql = "SELECT
            p.maPhong,
            p.dienTich,
            p.soChoLamViec,
            p.moTaViTri,
            p.donGiaM2,
            p.trangThai,
            t.soTang,
            t.heSoGia,
            c.tenCaoOc,
            c.diaChi,
            ROUND(p.donGiaM2 * p.dienTich * t.heSoGia, 0) AS giaThue
        FROM PHONG p
        JOIN TANG   t ON p.maTang  = t.maTang
        JOIN CAO_OC c ON t.maCaoOc = c.maCaoOc
        WHERE p.trangThai = 'Trống'";

// Thêm điều kiện lọc nếu có
if ($loc_dienTich_min > 0) {
    $sql .= " AND p.dienTich >= $loc_dienTich_min";
}
if ($loc_socho_min > 0) {
    $sql .= " AND p.soChoLamViec >= $loc_socho_min";
}
if (!empty($loc_timkiem)) {
    $kw = $conn->real_escape_string($loc_timkiem);
    $sql .= " AND (p.maPhong LIKE '%$kw%' OR c.tenCaoOc LIKE '%$kw%' OR p.moTaViTri LIKE '%$kw%')";
}

$sql .= " ORDER BY giaThue ASC LIMIT 12"; // Trang chủ: hiện tối đa 12 phòng nổi bật

$result = $conn->query($sql);

// ── Bước 5: Đếm tổng phòng trống (để hiển thị thống kê) ─────
$result_count = $conn->query("SELECT COUNT(*) AS total FROM PHONG WHERE trangThai = 'Trống'");
$totalPhongTrong = $result_count ? (int)$result_count->fetch_assoc()['total'] : 0;
?>

<?php include_once "includes/header.php"; ?>
<?php include_once "includes/navbar.php"; ?>
<?php include_once "includes/banner.php"; ?>

<!-- ══════════════════════════════════════════════════════════
     PHẦN THÂN TRANG CHỦ – DANH SÁCH PHÒNG ĐANG TRỐNG
     ══════════════════════════════════════════════════════════ -->
<main class="container my-5">

    <!-- ── TIÊU ĐỀ SECTION ── -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold mb-1">🚪 Phòng đang cho thuê</h2>
            <p class="text-muted mb-0">
                Hiện có <strong class="text-success"><?php echo $totalPhongTrong; ?> phòng</strong>
                đang trống, sẵn sàng nhận thuê ngay.
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="phong_trong.php" class="btn btn-outline-primary">
                Xem tất cả phòng <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <!-- ── THANH TÌM KIẾM NHANH (INLINE) ── -->
    <form method="GET" action="index.php"
          class="row g-2 mb-4 p-3 bg-light rounded-3 border">
        <div class="col-md-4">
            <input type="text" name="timkiem" class="form-control"
                   placeholder="🔍 Tìm theo mã phòng, tên cao ốc..."
                   value="<?php echo e($loc_timkiem); ?>">
        </div>
        <div class="col-md-3">
            <select name="dienTich_min" class="form-select">
                <option value="">Tất cả diện tích</option>
                <?php
                foreach ([30, 50, 80, 100, 150] as $dt) {
                    $sel = ($loc_dienTich_min == $dt) ? 'selected' : '';
                    echo "<option value=\"$dt\" $sel>Từ $dt m²</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
        </div>
        <div class="col-md-2">
            <a href="index.php" class="btn btn-outline-secondary w-100">Xóa lọc</a>
        </div>
    </form>

    <!-- ── LƯỚI CARD PHÒNG ── -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php
        if (!$result || $result->num_rows === 0):
        ?>
            <!-- Không có phòng phù hợp -->
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-info-circle fs-1 d-block mb-3 text-info"></i>
                    <h5>Không có phòng trống phù hợp với điều kiện lọc.</h5>
                    <p class="text-muted mb-3">Thử thay đổi bộ lọc hoặc liên hệ tư vấn viên để được hỗ trợ.</p>
                    <a href="index.php" class="btn btn-outline-primary me-2">Xóa bộ lọc</a>
                    <a href="lien_he.php" class="btn btn-primary">Liên hệ tư vấn</a>
                </div>
            </div>
        <?php
        else:
            // ── VÒNG LẶP HIỂN THỊ TỪNG PHÒNG DƯỚI DẠNG CARD ──
            while ($row = $result->fetch_assoc()):
        ?>
            <div class="col">
                <div class="card card-phong shadow-sm">

                    <!-- Ảnh đại diện phòng (placeholder SVG – thay bằng ảnh thực tế) -->
                    <div class="position-relative overflow-hidden"
                         style="height:180px; background: linear-gradient(135deg, #e8f0fe, #c3d3f0);">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <i class="bi bi-building" style="font-size:4rem; color:#4a7cc7; opacity:.4;"></i>
                        </div>
                        <!-- Badge "Trống" -->
                        <span class="badge bg-success position-absolute top-0 end-0 m-2 px-2 py-1">
                            ✅ Trống
                        </span>
                        <!-- Badge tầng -->
                        <span class="badge bg-dark bg-opacity-50 position-absolute bottom-0 start-0 m-2">
                            Tầng <?php echo e($row['soTang']); ?>
                        </span>
                    </div>

                    <!-- Nội dung card -->
                    <div class="card-body">

                        <!-- Mã phòng + tên cao ốc -->
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="card-title fw-bold mb-0 text-primary">
                                <?php echo e($row['maPhong']); ?>
                            </h6>
                            <small class="text-muted"><?php echo e($row['tenCaoOc']); ?></small>
                        </div>

                        <!-- Địa chỉ -->
                        <p class="text-muted small mb-2">
                            <i class="bi bi-geo-alt me-1"></i><?php echo e($row['diaChi']); ?>
                        </p>

                        <!-- Mô tả vị trí -->
                        <?php if (!empty($row['moTaViTri'])): ?>
                        <p class="small text-secondary mb-2"
                           style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                           title="<?php echo e($row['moTaViTri']); ?>">
                            📍 <?php echo e($row['moTaViTri']); ?>
                        </p>
                        <?php endif; ?>

                        <!-- Thông số (diện tích + số chỗ) -->
                        <div class="d-flex gap-3 text-muted small mb-3">
                            <span>
                                <i class="bi bi-aspect-ratio me-1 text-info"></i>
                                <?php echo number_format($row['dienTich'], 1); ?> m²
                            </span>
                            <span>
                                <i class="bi bi-people me-1 text-info"></i>
                                <?php echo e($row['soChoLamViec']); ?> chỗ
                            </span>
                        </div>

                        <!-- Giá thuê – nổi bật -->
                        <div class="gia-thue mb-3">
                            <?php echo dinhDangTien($row['giaThue']); ?>
                            <span class="text-muted fw-normal" style="font-size:.9rem;">/tháng</span>
                        </div>

                    </div><!-- end card-body -->

                    <!-- Nút hành động -->
                    <div class="card-footer bg-transparent border-top-0 pb-3 px-3">
                        <div class="d-grid gap-2">
                            <a href="dang_ky_thue.php?maPhong=<?php echo urlencode($row['maPhong']); ?>"
                               class="btn btn-gold btn-sm">
                                <i class="bi bi-pencil-square me-1"></i>Đăng ký thuê phòng này
                            </a>
                            <a href="chi_tiet_phong.php?id=<?php echo urlencode($row['maPhong']); ?>"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-eye me-1"></i>Xem chi tiết
                            </a>
                        </div>
                    </div>

                </div><!-- end .card-phong -->
            </div><!-- end .col -->
        <?php
            endwhile; // end while phòng
        endif;
        ?>
    </div><!-- end row card grid -->

    <!-- ── NÚT XEM THÊM (nếu còn phòng) ── -->
    <?php if ($totalPhongTrong > 12): ?>
    <div class="text-center mt-5">
        <p class="text-muted">Đang hiển thị 12 / <?php echo $totalPhongTrong; ?> phòng trống.</p>
        <a href="phong_trong.php" class="btn btn-brand px-5 py-2">
            <i class="bi bi-grid-3x3-gap me-2"></i>Xem toàn bộ <?php echo $totalPhongTrong; ?> phòng
        </a>
    </div>
    <?php endif; ?>

</main><!-- end main -->

<!-- ── SECTION: TẠI SAO CHỌN CHÚNG TÔI ────────────────────── -->
<section class="py-5" style="background-color: #f0f4f8;">
    <div class="container">
        <h3 class="fw-bold text-center mb-4">Tại sao chọn CAOCENTER?</h3>
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="p-4">
                    <i class="bi bi-shield-check fs-1 text-primary mb-3 d-block"></i>
                    <h6 class="fw-bold">An ninh 24/7</h6>
                    <p class="text-muted small">Hệ thống camera và bảo vệ chuyên nghiệp hoạt động liên tục.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="bi bi-lightning-charge fs-1 text-warning mb-3 d-block"></i>
                    <h6 class="fw-bold">Điện nước ổn định</h6>
                    <p class="text-muted small">Hệ thống điện dự phòng, nước sạch đảm bảo 24/24.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="bi bi-wifi fs-1 text-success mb-3 d-block"></i>
                    <h6 class="fw-bold">Internet tốc độ cao</h6>
                    <p class="text-muted small">Đường truyền fiber quang, backup 4G tốc độ cao.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4">
                    <i class="bi bi-headset fs-1 text-danger mb-3 d-block"></i>
                    <h6 class="fw-bold">Hỗ trợ tận tâm</h6>
                    <p class="text-muted small">Đội ngũ quản lý tòa nhà luôn sẵn sàng hỗ trợ.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Đóng kết nối DB sau khi tất cả logic đã xong
$conn->close();

// Module footer (chứa cả </body></html> và Bootstrap JS)
include_once "includes/footer.php";
?>