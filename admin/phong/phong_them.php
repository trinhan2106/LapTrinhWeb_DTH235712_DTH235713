<?php
/**
 * PHONG_THEM.PHP – Form thêm phòng mới
 *
 * Các điểm kỹ thuật quan trọng:
 *  1. Dropdown tầng được load từ DB (JOIN TANG – CAO_OC)
 *  2. Ô "Giá thuê/tháng" là READONLY – tự tính bằng JavaScript real-time
 *     (Công thức: donGiaM2 × dienTich × heSoGia)
 *  3. Khi form submit → POST sang phong_them_submit.php (không xử lý ở file này)
 */

// ── Bảo vệ trang ─────────────────────────────────────────────
require_once "../../thuvien.php";
kiemTraSession();

// ── Kết nối CSDL ─────────────────────────────────────────────
require_once "../../cauhinh.php";

// ── Load danh sách tầng để đổ vào dropdown ───────────────────
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
    <title>Thêm phòng mới – Quản lý Cao ốc</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- HEADER -->
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

    <!-- MENU BÊN TRÁI (giống phong_hienthi.php) -->
    <div class="col-md-2 bg-light border-end min-vh-100 pt-3 px-0">
        <div class="px-3">
            <p class="text-muted fw-bold small mb-1 mt-2">DANH MỤC</p>
            <ul class="nav flex-column">
                <li><a class="nav-link py-1 active" href="phong_hienthi.php">🚪 Quản lý Phòng</a></li>
                <li><a class="nav-link py-1" href="../cao_oc/cao_oc_hienthi.php">🏗️ Quản lý Cao ốc</a></li>
                <li><a class="nav-link py-1" href="../khach_hang/kh_hienthi.php">👥 Khách hàng</a></li>
            </ul>
        </div>
    </div>

    <!-- NỘI DUNG CHÍNH -->
    <div class="col-md-6 pt-3 px-4">

        <h5 class="fw-bold">➕ Thêm phòng mới</h5>
        <hr class="mt-1">

        <!-- Thông báo lỗi từ file _submit.php (truyền qua URL ?loi=...) -->
        <?php if (!empty($_GET['loi'])): ?>
            <div class="alert alert-danger py-2">
                ❌ <?php echo e($_GET['loi']); ?>
            </div>
        <?php endif; ?>

        <!--
            FORM THÊM PHÒNG
            method="POST" – gửi dữ liệu qua POST (không hiện trên URL)
            action="phong_them_submit.php" – file xử lý logic (không có HTML)
        -->
        <form method="POST" action="phong_them_submit.php" novalidate>

            <!-- Mã phòng -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Mã phòng <span class="text-danger">*</span>
                </label>
                <input type="text" name="maPhong" class="form-control"
                       placeholder="VD: P-00001" maxlength="10" required>
                <div class="form-text">Tối đa 10 ký tự, không được trùng với mã đã có</div>
            </div>

            <!-- Chọn tầng (load từ DB) -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Tầng (thuộc Cao ốc) <span class="text-danger">*</span>
                </label>
                <select name="maTang" id="maTang" class="form-select" required
                        onchange="capNhatHeSoVaTinhGia()">
                    <option value="">-- Chọn tầng --</option>
                    <?php
                    // Đổ dữ liệu tầng vào dropdown, kèm data-heso để JS dùng
                    while ($tang = $result_tang->fetch_assoc()) {
                        printf(
                            '<option value="%s" data-heso="%s">%s – Tầng %d (hệ số giá: %s)</option>',
                            e($tang['maTang']),
                            $tang['heSoGia'],
                            e($tang['tenCaoOc']),
                            $tang['soTang'],
                            $tang['heSoGia']
                        );
                    }
                    ?>
                </select>
            </div>

            <!-- Diện tích + Số chỗ (2 cột) -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        Diện tích sử dụng (m²) <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="dienTich" id="dienTich"
                           class="form-control" min="1" step="0.1" required
                           onchange="tinhGiaThuePreview()" oninput="tinhGiaThuePreview()">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        Số chỗ làm việc <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="soChoLamViec" class="form-control"
                           min="1" required>
                </div>
            </div>

            <!-- Đơn giá thuê/m² -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Đơn giá thuê/m²/tháng (VND) <span class="text-danger">*</span>
                </label>
                <input type="number" name="donGiaM2" id="donGiaM2"
                       class="form-control" min="1" step="1000" required
                       onchange="tinhGiaThuePreview()" oninput="tinhGiaThuePreview()">
            </div>

            <!-- Giá thuê tự động tính (READONLY – không cho sửa tay) -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Giá thuê/tháng (tự động tính)</label>
                <input type="text" id="giaThuePreview" class="form-control bg-light text-success fw-bold"
                       readonly placeholder="Nhập đơn giá và diện tích để xem...">
                <div class="form-text text-info">
                    💡 Công thức: <code>Đơn giá × Diện tích × Hệ số tầng</code>
                    — Ô này chỉ đọc, hệ thống tự tính.
                </div>
            </div>

            <!-- Mô tả vị trí -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Mô tả vị trí</label>
                <input type="text" name="moTaViTri" class="form-control"
                       placeholder="VD: Góc tây nam, view hồ bơi, gần thang máy">
            </div>

            <!-- Trạng thái -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Trạng thái ban đầu</label>
                <select name="trangThai" class="form-select">
                    <option value="Trống"    selected>🟢 Trống (mặc định)</option>
                    <option value="BaoTri"           >🟡 Bảo trì</option>
                </select>
            </div>

            <!-- Nút bấm -->
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success px-4">
                    💾 Lưu phòng
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
 * Hàm JavaScript tính giá thuê real-time (preview cho người dùng xem)
 * Công thức: donGiaM2 × dienTich × heSoGia
 * Kết quả hiển thị vào ô readonly "giaThuePreview"
 */
function tinhGiaThuePreview() {
    var donGia   = parseFloat(document.getElementById('donGiaM2').value)  || 0;
    var dienTich = parseFloat(document.getElementById('dienTich').value)   || 0;

    // Lấy hệ số từ data-heso của option đang chọn trong dropdown
    var selectEl = document.getElementById('maTang');
    var optionDangChon = selectEl.options[selectEl.selectedIndex];
    var heSo = parseFloat(optionDangChon ? optionDangChon.getAttribute('data-heso') : 1) || 1;

    var giaThuePreviewEl = document.getElementById('giaThuePreview');

    if (donGia > 0 && dienTich > 0) {
        var giaThue = Math.round(donGia * dienTich * heSo);
        // Định dạng số có dấu chấm phân cách hàng nghìn
        giaThuePreviewEl.value = giaThue.toLocaleString('vi-VN') + ' ₫';
    } else {
        giaThuePreviewEl.value = '';
        giaThuePreviewEl.placeholder = 'Nhập đơn giá và diện tích để xem...';
    }
}

// Khi đổi tầng → cập nhật hệ số → tính lại giá thuê
function capNhatHeSoVaTinhGia() {
    tinhGiaThuePreview();
}
</script>
</body>
</html>
<?php $conn->close(); ?>
