<?php
/**
 * PHONG_SUA.PHP – Form sửa thông tin phòng
 *
 * Luồng chạy:
 *   1. Nhận ?id= từ URL (maPhong cần sửa)
 *   2. Truy vấn DB lấy dữ liệu hiện tại của phòng đó
 *   3. Hiển thị form đã được pre-fill đầy đủ thông tin cũ
 *   4. Khi submit → POST sang phong_sua_submit.php
 *
 * Điểm kỹ thuật:
 *   - Dropdown tầng vẫn load từ DB, option đang chọn được đánh dấu "selected"
 *   - Ô "Giá thuê" vẫn là READONLY, JS tự tính real-time khi người dùng thay đổi
 */

// ── Bước 1: Bảo vệ trang ─────────────────────────────────────
require_once "../thuvien.php";
kiemTraSession();

// ── Bước 2: Kết nối CSDL ─────────────────────────────────────
require_once "../cauhinh.php";

// ── Bước 3: Lấy mã phòng từ URL và kiểm tra hợp lệ ──────────
$maPhong = trim($_GET['id'] ?? '');
if (empty($maPhong)) {
    header("Location: phong_hienthi.php?loi=" . urlencode("Thiếu mã phòng cần sửa!"));
    exit();
}

// ── Bước 4: Truy vấn thông tin phòng hiện tại từ DB ──────────
$maPhong_esc = $conn->real_escape_string($maPhong);
$sql_phong = "SELECT p.*, t.heSoGia, t.soTang, c.tenCaoOc
              FROM PHONG p
              JOIN TANG   t ON p.maTang  = t.maTang
              JOIN CAO_OC c ON t.maCaoOc = c.maCaoOc
              WHERE p.maPhong = '$maPhong_esc'";

$result_phong = $conn->query($sql_phong);

// Nếu không tìm thấy phòng → quay về danh sách kèm lỗi
if (!$result_phong || $result_phong->num_rows === 0) {
    header("Location: phong_hienthi.php?loi=" . urlencode("Không tìm thấy phòng '$maPhong'!"));
    exit();
}

$phong = $result_phong->fetch_assoc(); // Dữ liệu phòng hiện tại

// ── Bước 5: Load danh sách tầng cho dropdown ─────────────────
$sql_tang = "SELECT t.maTang, t.soTang, t.heSoGia, c.tenCaoOc
             FROM TANG t
             JOIN CAO_OC c ON t.maCaoOc = c.maCaoOc
             ORDER BY c.tenCaoOc, t.soTang";
$result_tang = $conn->query($sql_tang);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa phòng <?php echo e($phong['maPhong']); ?> – Quản lý Cao ốc</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- ── HEADER ─────────────────────────────────────────────── -->
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

    <!-- ── MENU BÊN TRÁI ──────────────────────────────────── -->
    <div class="col-md-2 bg-light border-end min-vh-100 pt-3 px-0">
        <div class="px-3">
            <p class="text-muted fw-bold small mb-1 mt-2">DANH MỤC</p>
            <ul class="nav flex-column">
                <li><a class="nav-link py-1 active fw-bold" href="phong_hienthi.php">🚪 Quản lý Phòng</a></li>
                <li><a class="nav-link py-1" href="../cao_oc/cao_oc_hienthi.php">🏗️ Quản lý Cao ốc</a></li>
                <li><a class="nav-link py-1" href="../khach_hang/kh_hienthi.php">👥 Khách hàng</a></li>
            </ul>
            <p class="text-muted fw-bold small mb-1 mt-3">HỢP ĐỒNG</p>
            <ul class="nav flex-column">
                <li><a class="nav-link py-1" href="../hop_dong/hd_hienthi.php">📃 Danh sách HĐ</a></li>
                <li><a class="nav-link py-1" href="../hop_dong/hd_them.php">➕ Lập hợp đồng</a></li>
            </ul>
            <p class="text-muted fw-bold small mb-1 mt-3">THANH TOÁN</p>
            <ul class="nav flex-column">
                <li><a class="nav-link py-1" href="../thanh_toan/tt_tao.php">💳 Lập hóa đơn</a></li>
            </ul>
        </div>
    </div><!-- end menu -->

    <!-- ── NỘI DUNG CHÍNH ─────────────────────────────────── -->
    <div class="col-md-6 pt-3 px-4">

        <h5 class="fw-bold">✏️ Sửa thông tin phòng:
            <span class="text-primary"><?php echo e($phong['maPhong']); ?></span>
        </h5>
        <hr class="mt-1">

        <!-- Thông báo lỗi từ submit (truyền qua URL ?loi=...) -->
        <?php if (!empty($_GET['loi'])): ?>
            <div class="alert alert-danger py-2">
                ❌ <?php echo e($_GET['loi']); ?>
            </div>
        <?php endif; ?>

        <!--
            FORM SỬA PHÒNG
            method="POST" → gửi POST sang phong_sua_submit.php
            Truyền kèm maPhong qua hidden field để submit biết sửa phòng nào
        -->
        <form method="POST" action="phong_sua_submit.php" novalidate>

            <!-- Hidden field: giữ maPhong để submit biết sửa phòng nào -->
            <input type="hidden" name="maPhong" value="<?php echo e($phong['maPhong']); ?>">

            <!-- Mã phòng – CHỈ ĐỌC, không cho đổi khóa chính -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Mã phòng</label>
                <input type="text" class="form-control bg-light text-muted"
                       value="<?php echo e($phong['maPhong']); ?>" readonly>
                <div class="form-text text-muted">Mã phòng không thể thay đổi sau khi tạo.</div>
            </div>

            <!-- Chọn tầng – pre-select tầng hiện tại -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Tầng (thuộc Cao ốc) <span class="text-danger">*</span>
                </label>
                <select name="maTang" id="maTang" class="form-select" required
                        onchange="capNhatHeSoVaTinhGia()">
                    <option value="">-- Chọn tầng --</option>
                    <?php
                    // Đổ danh sách tầng; đánh dấu "selected" nếu trùng với tầng hiện tại
                    while ($tang = $result_tang->fetch_assoc()) {
                        $selected = ($tang['maTang'] === $phong['maTang']) ? 'selected' : '';
                        printf(
                            '<option value="%s" data-heso="%s" %s>%s – Tầng %d (hệ số: %s)</option>',
                            e($tang['maTang']),
                            $tang['heSoGia'],
                            $selected,
                            e($tang['tenCaoOc']),
                            $tang['soTang'],
                            $tang['heSoGia']
                        );
                    }
                    ?>
                </select>
            </div>

            <!-- Diện tích + Số chỗ (2 cột) – pre-fill giá trị cũ -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        Diện tích (m²) <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="dienTich" id="dienTich"
                           class="form-control" min="1" step="0.1" required
                           value="<?php echo $phong['dienTich']; ?>"
                           onchange="tinhGiaThuePreview()" oninput="tinhGiaThuePreview()">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        Số chỗ làm việc <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="soChoLamViec" class="form-control"
                           min="1" required
                           value="<?php echo $phong['soChoLamViec']; ?>">
                </div>
            </div>

            <!-- Đơn giá thuê – pre-fill giá trị cũ -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Đơn giá thuê/m²/tháng (VND) <span class="text-danger">*</span>
                </label>
                <input type="number" name="donGiaM2" id="donGiaM2"
                       class="form-control" min="1" step="1000" required
                       value="<?php echo $phong['donGiaM2']; ?>"
                       onchange="tinhGiaThuePreview()" oninput="tinhGiaThuePreview()">
            </div>

            <!-- Giá thuê tự động tính (READONLY) – hiển thị giá thuê hiện tại -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Giá thuê/tháng (tự động tính)</label>
                <input type="text" id="giaThuePreview"
                       class="form-control bg-light text-success fw-bold"
                       readonly
                       value="<?php echo number_format($phong['giaThue'], 0, ',', '.') . ' ₫'; ?>">
                <div class="form-text text-info">
                    💡 Công thức: <code>Đơn giá × Diện tích × Hệ số tầng</code> — Ô chỉ đọc.
                </div>
            </div>

            <!-- Mô tả vị trí – pre-fill giá trị cũ -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Mô tả vị trí</label>
                <input type="text" name="moTaViTri" class="form-control"
                       value="<?php echo e($phong['moTaViTri'] ?? ''); ?>"
                       placeholder="VD: Góc tây nam, view hồ bơi">
            </div>

            <!-- Trạng thái – pre-select trạng thái hiện tại -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Trạng thái</label>
                <select name="trangThai" class="form-select">
                    <option value="Trống"
                        <?php if ($phong['trangThai'] === 'Trống')    echo 'selected'; ?>>
                        🟢 Trống
                    </option>
                    <option value="DangThue"
                        <?php if ($phong['trangThai'] === 'DangThue') echo 'selected'; ?>>
                        🔴 Đang thuê
                    </option>
                    <option value="BaoTri"
                        <?php if ($phong['trangThai'] === 'BaoTri')   echo 'selected'; ?>>
                        🟡 Bảo trì
                    </option>
                </select>
                <div class="form-text text-warning">
                    ⚠️ Thay đổi trạng thái thủ công chỉ dùng khi cần thiết (bảo trì, sửa lỗi dữ liệu).
                </div>
            </div>

            <!-- Nút bấm -->
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-warning px-4">
                    💾 Cập nhật thông tin
                </button>
                <a href="phong_hienthi.php" class="btn btn-outline-secondary">
                    ↩ Quay lại danh sách
                </a>
            </div>

            <p class="mt-3 text-muted small">
                <span class="text-danger">*</span> Các trường có dấu sao là bắt buộc
            </p>

        </form>
    </div><!-- end col-md-6 -->
</div><!-- end row -->
</div><!-- end container-fluid -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/**
 * Tính giá thuê real-time (giống phong_them.php)
 * Công thức: donGiaM2 × dienTich × heSoGia
 */
function tinhGiaThuePreview() {
    var donGia   = parseFloat(document.getElementById('donGiaM2').value)  || 0;
    var dienTich = parseFloat(document.getElementById('dienTich').value)   || 0;

    var selectEl = document.getElementById('maTang');
    var optionDangChon = selectEl.options[selectEl.selectedIndex];
    var heSo = parseFloat(optionDangChon ? optionDangChon.getAttribute('data-heso') : 1) || 1;

    var previewEl = document.getElementById('giaThuePreview');

    if (donGia > 0 && dienTich > 0) {
        var giaThue = Math.round(donGia * dienTich * heSo);
        previewEl.value = giaThue.toLocaleString('vi-VN') + ' ₫';
    } else {
        previewEl.value = '';
    }
}

// Khi đổi tầng → tính lại giá thuê
function capNhatHeSoVaTinhGia() {
    tinhGiaThuePreview();
}

// Tự tính giá thuê ngay khi trang load (dựa trên dữ liệu cũ đã pre-fill)
window.addEventListener('DOMContentLoaded', function() {
    tinhGiaThuePreview();
});
</script>
</body>
</html>
<?php $conn->close(); ?>