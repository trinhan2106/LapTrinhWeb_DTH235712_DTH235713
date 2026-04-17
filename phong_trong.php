<?php
/**
 * PHONG_TRONG.PHP – Danh sách tất cả phòng đang trống (Trang Public)
 *
 * Khác với index.php (chỉ hiện 12 phòng nổi bật), trang này:
 *   - Hiển thị TOÀN BỘ phòng trống, có phân trang
 *   - Có bộ lọc nâng cao: theo Cao ốc, Tầng, Giá min-max, Diện tích
 *   - Tất cả filter đều dùng tham số GET trên URL
 *
 * Luồng:
 *   1. Kết nối CSDL
 *   2. Load dữ liệu filter động từ DB (cao ốc, tầng)
 *   3. Xây dựng SQL có điều kiện WHERE động
 *   4. Phân trang đơn giản (LIMIT + OFFSET)
 *   5. Render trang bằng include các module
 */

// ── Bước 1: Kết nối và thư viện ───────────────────────────────
require_once "cauhinh.php";  // Khởi tạo $conn
require_once "thuvien.php";  // Hàm e(), dinhDangTien(), tinhGiaThue()...

// ── Bước 2: Biến cho modules include ──────────────────────────
$pageTitle   = "Danh sách phòng trống";
$currentPage = "phong_trong";
$showSearch  = false;  // Banner không có ô search (trang này đã có filter riêng)
$bannerTitle    = 'Danh sách phòng <span class="text-warning">đang trống</span>';
$bannerSubtitle = 'Tất cả các văn phòng sẵn sàng nhận thuê ngay. Liên hệ để đặt lịch tham quan miễn phí.';

// ── Bước 3: Nhận tham số lọc từ URL ───────────────────────────
$loc_maCaoOc     = trim($_GET['maCaoOc']     ?? '');
$loc_maTang      = trim($_GET['maTang']      ?? '');
$loc_giaMin      = (int)($_GET['giaMin']     ?? 0);
$loc_giaMax      = (int)($_GET['giaMax']     ?? 0);
$loc_dienTichMin = (int)($_GET['dienTich']   ?? 0);
$loc_timkiem     = trim($_GET['timkiem']     ?? '');

// ── Bước 4: Phân trang ────────────────────────────────────────
$soPhongMoiTrang = 9;                          // Hiện 9 card mỗi trang (lưới 3×3)
$trangHienTai    = max(1, (int)($_GET['trang'] ?? 1));
$offset          = ($trangHienTai - 1) * $soPhongMoiTrang;

// ── Bước 5: Load danh sách Cao ốc cho dropdown filter ─────────
// Không hardcode – lấy động từ DB
$sql_caoc = "SELECT maCaoOc, tenCaoOc FROM CAO_OC ORDER BY tenCaoOc";
$result_caoc = $conn->query($sql_caoc);
$dsCaoOc = [];  // Mảng lưu để dùng nhiều lần
while ($row_caoc = $result_caoc->fetch_assoc()) {
    $dsCaoOc[] = $row_caoc;
}

// ── Bước 6: Load danh sách Tầng (lọc theo Cao ốc nếu có) ──────
$sql_tang = "SELECT t.maTang, t.soTang, c.tenCaoOc
             FROM TANG t JOIN CAO_OC c ON t.maCaoOc = c.maCaoOc";
// Nếu đã chọn cao ốc → chỉ load tầng của cao ốc đó
if (!empty($loc_maCaoOc)) {
    $maCaoOc_esc = $conn->real_escape_string($loc_maCaoOc);
    $sql_tang   .= " WHERE t.maCaoOc = '$maCaoOc_esc'";
}
$sql_tang   .= " ORDER BY c.tenCaoOc, t.soTang";
$result_tang = $conn->query($sql_tang);
$dsTang = [];
while ($row_tang = $result_tang->fetch_assoc()) {
    $dsTang[] = $row_tang;
}

// ── Bước 7: Xây dựng câu SQL lấy danh sách phòng trống ────────
$sql_base = "FROM PHONG p
             JOIN TANG   t ON p.maTang  = t.maTang
             JOIN CAO_OC c ON t.maCaoOc = c.maCaoOc
             WHERE p.trangThai = 'Trống'";

// Thêm điều kiện lọc động (chống SQL Injection bằng real_escape_string)
if (!empty($loc_maCaoOc)) {
    $v = $conn->real_escape_string($loc_maCaoOc);
    $sql_base .= " AND c.maCaoOc = '$v'";
}
if (!empty($loc_maTang)) {
    $v = $conn->real_escape_string($loc_maTang);
    $sql_base .= " AND t.maTang = '$v'";
}
if ($loc_giaMin > 0) {
    // So sánh với giá thuê tính động (không dùng cột giaThue lưu sẵn)
    $sql_base .= " AND ROUND(p.donGiaM2 * p.dienTich * t.heSoGia, 0) >= $loc_giaMin";
}
if ($loc_giaMax > 0 && $loc_giaMax > $loc_giaMin) {
    $sql_base .= " AND ROUND(p.donGiaM2 * p.dienTich * t.heSoGia, 0) <= $loc_giaMax";
}
if ($loc_dienTichMin > 0) {
    $sql_base .= " AND p.dienTich >= $loc_dienTichMin";
}
if (!empty($loc_timkiem)) {
    $kw = $conn->real_escape_string($loc_timkiem);
    $sql_base .= " AND (p.maPhong LIKE '%$kw%' OR c.tenCaoOc LIKE '%$kw%' OR p.moTaViTri LIKE '%$kw%')";
}

// Đếm tổng số phòng phù hợp (để tính phân trang)
$sql_count  = "SELECT COUNT(*) AS total " . $sql_base;
$res_count  = $conn->query($sql_count);
$tongPhong  = $res_count ? (int)$res_count->fetch_assoc()['total'] : 0;
$tongTrang  = ($tongPhong > 0) ? ceil($tongPhong / $soPhongMoiTrang) : 1;

// Truy vấn chính lấy dữ liệu trang hiện tại
$sql_phong  = "SELECT p.maPhong, p.dienTich, p.soChoLamViec, p.moTaViTri, p.donGiaM2,
                      t.soTang, t.heSoGia,
                      c.tenCaoOc, c.diaChi,
                      ROUND(p.donGiaM2 * p.dienTich * t.heSoGia, 0) AS giaThue
               " . $sql_base . "
               ORDER BY giaThue ASC
               LIMIT $soPhongMoiTrang OFFSET $offset";

$result_phong = $conn->query($sql_phong);

// ── Xây dựng query string cho phân trang (giữ nguyên tham số lọc) ──
$queryFilter = http_build_query([
    'maCaoOc'  => $loc_maCaoOc,
    'maTang'   => $loc_maTang,
    'giaMin'   => $loc_giaMin,
    'giaMax'   => $loc_giaMax,
    'dienTich' => $loc_dienTichMin,
    'timkiem'  => $loc_timkiem,
]);
?>

<?php include_once "includes/header.php"; ?>
<?php include_once "includes/navbar.php"; ?>
<?php include_once "includes/banner.php"; ?>

<!-- ══════════════════════════════════════════════════════════
     PHẦN THÂN – DANH SÁCH PHÒNG TRỐNG ĐẦY ĐỦ VỚI FILTER
     ══════════════════════════════════════════════════════════ -->
<main class="container my-5">

    <div class="row g-4">

        <!-- ── CỘT TRÁI: BỘ LỌC NÂNG CAO (3/12) ── -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 80px;">
                <div class="card-body p-4">

                    <h6 class="fw-bold mb-3 text-primary">
                        <i class="bi bi-funnel me-2"></i>Bộ lọc tìm kiếm
                    </h6>

                    <!--
                        Form lọc: method GET để kết quả lọc có thể share bằng URL.
                        Tất cả dropdown đều load từ DB, không hardcode.
                    -->
                    <form method="GET" action="phong_trong.php" id="formLoc">

                        <!-- Tìm kiếm từ khóa -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Từ khóa</label>
                            <input type="text" name="timkiem" class="form-control form-control-sm"
                                   placeholder="Mã phòng, tên cao ốc..."
                                   value="<?php echo e($loc_timkiem); ?>">
                        </div>

                        <!-- Lọc theo Cao ốc – load động từ DB -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Cao ốc</label>
                            <select name="maCaoOc" class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                <option value="">-- Tất cả cao ốc --</option>
                                <?php
                                // Vòng lặp render option từ mảng đã load ở trên
                                foreach ($dsCaoOc as $caoc) {
                                    $sel = ($caoc['maCaoOc'] === $loc_maCaoOc) ? 'selected' : '';
                                    printf(
                                        '<option value="%s" %s>%s</option>',
                                        e($caoc['maCaoOc']),
                                        $sel,
                                        e($caoc['tenCaoOc'])
                                    );
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Lọc theo Tầng – load động từ DB (đã lọc theo cao ốc) -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tầng</label>
                            <select name="maTang" class="form-select form-select-sm">
                                <option value="">-- Tất cả tầng --</option>
                                <?php
                                foreach ($dsTang as $tang) {
                                    $sel = ($tang['maTang'] === $loc_maTang) ? 'selected' : '';
                                    printf(
                                        '<option value="%s" %s>%s – Tầng %d</option>',
                                        e($tang['maTang']),
                                        $sel,
                                        e($tang['tenCaoOc']),
                                        $tang['soTang']
                                    );
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Lọc theo Diện tích tối thiểu -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Diện tích tối thiểu</label>
                            <select name="dienTich" class="form-select form-select-sm">
                                <option value="0">Không giới hạn</option>
                                <?php
                                // Các mốc diện tích lọc (đây là tùy chọn UI, không phải dữ liệu nghiệp vụ)
                                foreach ([30 => '≥ 30 m²', 50 => '≥ 50 m²', 80 => '≥ 80 m²',
                                          100 => '≥ 100 m²', 150 => '≥ 150 m²'] as $val => $lbl) {
                                    $sel = ($loc_dienTichMin == $val) ? 'selected' : '';
                                    printf('<option value="%d" %s>%s</option>', $val, $sel, $lbl);
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Lọc theo Khoảng giá -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Giá thuê (₫/tháng)</label>
                            <div class="d-flex gap-2">
                                <input type="number" name="giaMin" class="form-control form-control-sm"
                                       placeholder="Từ" min="0" step="1000000"
                                       value="<?php echo $loc_giaMin > 0 ? $loc_giaMin : ''; ?>">
                                <input type="number" name="giaMax" class="form-control form-control-sm"
                                       placeholder="Đến" min="0" step="1000000"
                                       value="<?php echo $loc_giaMax > 0 ? $loc_giaMax : ''; ?>">
                            </div>
                        </div>

                        <!-- Nút Lọc và Xóa lọc -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                <i class="bi bi-funnel me-1"></i>Áp dụng bộ lọc
                            </button>
                            <a href="phong_trong.php" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Xóa tất cả lọc
                            </a>
                        </div>

                    </form><!-- end formLoc -->

                </div>
            </div><!-- end card sticky -->
        </div><!-- end col-lg-3 -->

        <!-- ── CỘT PHẢI: KẾT QUẢ PHÒNG (9/12) ── -->
        <div class="col-lg-9">

            <!-- Thông tin kết quả -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    🚪 Phòng trống
                    <span class="badge bg-success ms-2"><?php echo $tongPhong; ?></span>
                </h5>
                <span class="text-muted small">
                    Trang <?php echo $trangHienTai; ?> / <?php echo $tongTrang; ?>
                </span>
            </div>

            <!-- LƯỚI CARD PHÒNG -->
            <?php if (!$result_phong || $result_phong->num_rows === 0): ?>

                <!-- Không có kết quả -->
                <div class="alert alert-info text-center py-5 rounded-4">
                    <i class="bi bi-info-circle fs-1 text-info d-block mb-3"></i>
                    <h5>Không có phòng nào phù hợp với bộ lọc hiện tại.</h5>
                    <p class="text-muted mb-3">Thử điều chỉnh điều kiện lọc hoặc xóa bớt tiêu chí.</p>
                    <a href="phong_trong.php" class="btn btn-outline-primary">
                        <i class="bi bi-x-circle me-1"></i>Xóa tất cả bộ lọc
                    </a>
                </div>

            <?php else: ?>

                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                    <?php
                    // Vòng lặp render từng phòng dạng card
                    while ($row = $result_phong->fetch_assoc()):
                    ?>
                    <div class="col">
                        <div class="card card-phong shadow-sm h-100">

                            <!-- Ảnh đại diện (placeholder) + Badge -->
                            <div class="position-relative overflow-hidden card-phong-img-placeholder">
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <i class="bi bi-building" style="font-size:5rem;"></i>
                                </div>
                                <span class="badge bg-white text-dark border border-dark position-absolute top-0 end-0 m-2 px-2 py-1" style="font-family: var(--font-mono); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Trống
                                </span>
                                <span class="badge bg-white text-dark border border-dark position-absolute bottom-0 start-0 m-2" style="font-family: var(--font-mono); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    <?php echo e($row['tenCaoOc']); ?> – Tầng <?php echo e($row['soTang']); ?>
                                </span>
                            </div>

                            <!-- Nội dung card -->
                            <div class="card-body p-3">
                                <!-- Mã phòng -->
                                <h6 class="card-title mb-1 card-phong-title">
                                    <?php echo e($row['maPhong']); ?>
                                </h6>

                                <!-- Địa chỉ cao ốc -->
                                <p class="text-muted small mb-2" style="font-size:.8rem;">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo e($row['diaChi']); ?>
                                </p>

                                <!-- Mô tả vị trí (nếu có) -->
                                <?php if (!empty($row['moTaViTri'])): ?>
                                <p class="small text-secondary mb-2"
                                   style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-size:.8rem;"
                                   title="<?php echo e($row['moTaViTri']); ?>">
                                    📍 <?php echo e($row['moTaViTri']); ?>
                                </p>
                                <?php endif; ?>

                                <!-- Thông số -->
                                <div class="d-flex gap-3 text-muted mb-2" style="font-size:.82rem;">
                                    <span><i class="bi bi-aspect-ratio me-1 text-info"></i>
                                        <?php echo number_format($row['dienTich'], 1); ?> m²
                                    </span>
                                    <span><i class="bi bi-people me-1 text-info"></i>
                                        <?php echo e($row['soChoLamViec']); ?> chỗ
                                    </span>
                                </div>

                                <!-- Giá thuê nổi bật -->
                                <div class="gia-thue">
                                    <?php echo dinhDangTien($row['giaThue']); ?>
                                    <span class="text-muted fw-normal" style="font-size:.85rem;">/tháng</span>
                                </div>
                            </div><!-- end card-body -->

                            <!-- Nút hành động -->
                            <div class="card-footer bg-transparent border-0 pb-4 px-3 pt-0">
                                <div class="d-flex flex-column gap-2">
                                    <a href="dang_ky_thue.php?maPhong=<?php echo urlencode($row['maPhong']); ?>"
                                       class="btn btn-premium-gold w-100" style="padding: 0.6rem 1rem;">
                                        Đăng ký thuê
                                    </a>
                                    <a href="chi_tiet_phong.php?id=<?php echo urlencode($row['maPhong']); ?>"
                                       class="btn-premium-link w-100">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>

                        </div><!-- end card-phong -->
                    </div><!-- end col -->
                    <?php endwhile; ?>
                </div><!-- end row cards -->

                <!-- ── PHÂN TRANG BOOTSTRAP ── -->
                <?php if ($tongTrang > 1): ?>
                <nav class="mt-4" aria-label="Phân trang danh sách phòng">
                    <ul class="pagination justify-content-center">

                        <!-- Trang trước -->
                        <li class="page-item <?php echo ($trangHienTai <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link"
                               href="?<?php echo $queryFilter; ?>&trang=<?php echo $trangHienTai - 1; ?>">
                                ← Trước
                            </a>
                        </li>

                        <!-- Các trang số -->
                        <?php for ($i = 1; $i <= $tongTrang; $i++): ?>
                        <li class="page-item <?php echo ($i === $trangHienTai) ? 'active' : ''; ?>">
                            <a class="page-link"
                               href="?<?php echo $queryFilter; ?>&trang=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <!-- Trang sau -->
                        <li class="page-item <?php echo ($trangHienTai >= $tongTrang) ? 'disabled' : ''; ?>">
                            <a class="page-link"
                               href="?<?php echo $queryFilter; ?>&trang=<?php echo $trangHienTai + 1; ?>">
                                Tiếp →
                            </a>
                        </li>

                    </ul>
                </nav>
                <?php endif; ?>

            <?php endif; // end if kết quả ?>

        </div><!-- end col-lg-9 -->

    </div><!-- end row -->

</main><!-- end main -->

<?php
// Đóng kết nối DB sau khi xử lý xong
$conn->close();

// Gọi footer (chứa </body></html> và Bootstrap JS)
include_once "includes/footer.php";
?>
