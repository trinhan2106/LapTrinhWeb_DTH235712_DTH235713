<?php
/**
 * PHONG_HIENTHI.PHP – Danh sách Phòng
 * Hiển thị bảng phòng với JOIN PHONG – TANG – CAO_OC
 * Có tìm kiếm theo mã/tên cao ốc và lọc theo trạng thái
 */

// ── Bước 1: Kiểm tra đăng nhập ──────────────────────────────
require_once "../../thuvien.php";
kiemTraSession();

// ── Bước 2: Kết nối CSDL ────────────────────────────────────
require_once "../../cauhinh.php";

// ── Bước 3: Nhận tham số lọc từ URL ($_GET) ─────────────────
$loc_trangThai = trim($_GET['trangThai'] ?? '');
$tim_kiem      = trim($_GET['timKiem']   ?? '');

// Hiển thị thông báo thành công từ lần thêm/sửa trước (nếu có)
$thong_bao_thanh_cong = trim($_GET['thanhcong'] ?? '');

// ── Bước 4: Xây dựng câu truy vấn (có điều kiện lọc động) ───
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
            ROUND(p.donGiaM2 * p.dienTich * t.heSoGia, 0) AS giaThue
        FROM PHONG p
        JOIN TANG   t ON p.maTang   = t.maTang
        JOIN CAO_OC c ON t.maCaoOc  = c.maCaoOc
        WHERE 1=1";

// Thêm điều kiện lọc nếu người dùng có chọn
if (!empty($loc_trangThai)) {
    $loc_trangThai_esc = $conn->real_escape_string($loc_trangThai);
    $sql .= " AND p.trangThai = '$loc_trangThai_esc'";
}
if (!empty($tim_kiem)) {
    $tim_kiem_esc = $conn->real_escape_string($tim_kiem);
    $sql .= " AND (p.maPhong    LIKE '%$tim_kiem_esc%'
               OR  c.tenCaoOc   LIKE '%$tim_kiem_esc%'
               OR  p.moTaViTri  LIKE '%$tim_kiem_esc%')";
}
$sql .= " ORDER BY c.tenCaoOc, t.soTang, p.maPhong";

$result = $conn->query($sql);
if (!$result) {
    die("Lỗi truy vấn: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Phòng – Quản lý Cao ốc</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- ── HEADER ────────────────────────────────────────────── -->
<nav class="navbar navbar-dark bg-primary px-3 py-2">
    <span class="navbar-brand fw-bold fs-5">🏢 Quản lý Cao ốc</span>
    <span class="text-white small">
        Xin chào: <strong><?php echo e($_SESSION['tenNV']); ?></strong>
        &nbsp;|&nbsp;
        <a href="../dangxuat.php" class="text-white-50">Đăng xuất</a>
    </span>
</nav>

<div class="container-fluid">
<div class="row">

    <!-- ── MENU BÊN TRÁI ───────────────────────────────────── -->
    <div class="col-md-2 bg-light border-end min-vh-100 pt-3 px-0">
        <div class="px-3">
            <p class="text-muted fw-bold small mb-1 mt-2">HỢP ĐỒNG</p>
            <ul class="nav flex-column mb-2">
                <li><a class="nav-link py-1" href="../hop_dong/hd_hienthi.php">📃 Danh sách HĐ</a></li>
                <li><a class="nav-link py-1" href="../hop_dong/hd_them.php">➕ Lập hợp đồng</a></li>
                <li><a class="nav-link py-1" href="../hop_dong/hd_gia_han.php">🔄 Gia hạn HĐ</a></li>
                <li><a class="nav-link py-1" href="../hop_dong/hd_huy.php">❌ Hủy HĐ</a></li>
            </ul>
            <p class="text-muted fw-bold small mb-1 mt-3">DANH MỤC</p>
            <ul class="nav flex-column mb-2">
                <li><a class="nav-link py-1 active fw-bold" href="phong_hienthi.php">🚪 Quản lý Phòng</a></li>
                <li><a class="nav-link py-1" href="../cao_oc/cao_oc_hienthi.php">🏗️ Quản lý Cao ốc</a></li>
                <li><a class="nav-link py-1" href="../khach_hang/kh_hienthi.php">👥 Khách hàng</a></li>
            </ul>
            <p class="text-muted fw-bold small mb-1 mt-3">THANH TOÁN</p>
            <ul class="nav flex-column mb-2">
                <li><a class="nav-link py-1" href="../thanh_toan/tt_tao.php">💳 Lập hóa đơn</a></li>
                <li><a class="nav-link py-1" href="../thanh_toan/dien_nuoc_ghi.php">⚡ Điện/Nước</a></li>
            </ul>
            <p class="text-muted fw-bold small mb-1 mt-3">BÁO CÁO</p>
            <ul class="nav flex-column">
                <li><a class="nav-link py-1" href="../bao_cao/bao_cao.php">📊 Xem báo cáo</a></li>
            </ul>
        </div>
    </div><!-- end menu -->

    <!-- ── NỘI DUNG CHÍNH ──────────────────────────────────── -->
    <div class="col-md-10 pt-3 px-4">

        <h5 class="fw-bold">🚪 Danh sách Phòng</h5>
        <hr class="mt-1">

        <!-- Thông báo thành công (sau khi thêm/sửa) -->
        <?php if (!empty($thong_bao_thanh_cong)): ?>
            <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                ✅ <?php echo e($thong_bao_thanh_cong); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ── THANH TÌM KIẾM VÀ LỌC ── -->
        <form method="GET" action="phong_hienthi.php" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="timKiem" class="form-control form-control-sm"
                       placeholder="🔍 Tìm theo mã phòng, tên cao ốc..."
                       value="<?php echo e($tim_kiem); ?>">
            </div>
            <div class="col-md-3">
                <select name="trangThai" class="form-select form-select-sm">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="Trống"    <?php if ($loc_trangThai == 'Trống')    echo 'selected'; ?>>🟢 Trống</option>
                    <option value="DangThue" <?php if ($loc_trangThai == 'DangThue') echo 'selected'; ?>>🔴 Đang thuê</option>
                    <option value="BaoTri"   <?php if ($loc_trangThai == 'BaoTri')   echo 'selected'; ?>>🟡 Bảo trì</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Lọc</button>
            </div>
            <div class="col-md-2">
                <a href="phong_hienthi.php" class="btn btn-outline-secondary btn-sm w-100">Xóa lọc</a>
            </div>
            <div class="col-md-1 text-end">
                <a href="phong_them.php" class="btn btn-success btn-sm">➕ Thêm</a>
            </div>
        </form>

        <!-- ── BẢNG DANH SÁCH PHÒNG ── -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Mã phòng</th>
                        <th>Cao ốc</th>
                        <th>Tầng</th>
                        <th>Diện tích (m²)</th>
                        <th>Số chỗ</th>
                        <th>Đơn giá/m²</th>
                        <th>Giá thuê/tháng</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows == 0) {
                    echo '<tr><td colspan="9" class="text-center text-muted py-3">
                            Không có dữ liệu phù hợp với điều kiện lọc.
                          </td></tr>';
                } else {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td class="fw-bold">' . e($row['maPhong']) . '</td>';
                        echo '<td>' . e($row['tenCaoOc']) . '</td>';
                        echo '<td class="text-center">Tầng ' . e($row['soTang']) . '</td>';
                        echo '<td class="text-end">' . number_format($row['dienTich'], 1) . ' m²</td>';
                        echo '<td class="text-center">' . e($row['soChoLamViec']) . ' chỗ</td>';
                        echo '<td class="text-end">' . dinhDangTien($row['donGiaM2']) . '</td>';
                        echo '<td class="text-end fw-bold text-success">' . dinhDangTien($row['giaThue']) . '</td>';
                        echo '<td class="text-center">' . badgeTrangThaiPhong($row['trangThai']) . '</td>';
                        echo '<td class="text-center">
                                <a href="phong_sua.php?id=' . urlencode($row['maPhong']) . '"
                                   class="btn btn-warning btn-sm" title="Sửa">✏️</a>
                                <a href="phong_xoa.php?id=' . urlencode($row['maPhong']) . '"
                                   class="btn btn-danger btn-sm" title="Xóa"
                                   onclick="return confirm(\'Bạn có chắc muốn xóa phòng ' . e($row['maPhong']) . '?\')">🗑️</a>
                              </td>';
                        echo '</tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <p class="text-muted small">Tổng số: <strong><?php echo $result->num_rows; ?></strong> phòng</p>

    </div><!-- end col-md-10 -->
</div><!-- end row -->
</div><!-- end container-fluid -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
