<?php
/**
 * PHONG_SUA_SUBMIT.PHP – Xử lý logic cập nhật phòng
 *
 * FILE NÀY KHÔNG CÓ HTML.
 * Luồng chạy:
 *   1. Chặn GET – chỉ nhận POST
 *   2. Nhận dữ liệu từ form phong_sua.php
 *   3. Validate dữ liệu đầu vào
 *   4. Lấy heSoGia từ DB để tính lại giaThue (server-side)
 *   5. UPDATE bảng PHONG
 *   6. Redirect về danh sách kèm thông báo
 */

// ── Bước 0: Chỉ chấp nhận POST ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: phong_hienthi.php");
    exit();
}

// ── Kết nối CSDL ─────────────────────────────────────────────
require_once "../cauhinh.php";

// ── Bước 1: Nhận dữ liệu từ $_POST ──────────────────────────
$maPhong      = trim($_POST['maPhong']      ?? '');
$maTang       = trim($_POST['maTang']       ?? '');
$dienTich     = trim($_POST['dienTich']     ?? '');
$soChoLamViec = trim($_POST['soChoLamViec'] ?? '');
$donGiaM2     = trim($_POST['donGiaM2']     ?? '');
$moTaViTri    = trim($_POST['moTaViTri']    ?? '');
$trangThai    = trim($_POST['trangThai']    ?? 'Trống');

// ── Bước 2: VALIDATE ─────────────────────────────────────────
$loi = [];

// Kiểm tra maPhong có tồn tại không (không cho sửa phòng không có trong DB)
if (empty($maPhong)) {
    $loi[] = "Thiếu mã phòng – không thể cập nhật!";
}

if (empty($maTang)) {
    $loi[] = "Vui lòng chọn tầng cho phòng!";
}

if ($dienTich === '' || !is_numeric($dienTich) || (float)$dienTich <= 0) {
    $loi[] = "Diện tích phải là số dương (lớn hơn 0)!";
}

if ($soChoLamViec === '' || !is_numeric($soChoLamViec) || (int)$soChoLamViec <= 0) {
    $loi[] = "Số chỗ làm việc phải là số nguyên dương!";
}

if ($donGiaM2 === '' || !is_numeric($donGiaM2) || (float)$donGiaM2 <= 0) {
    $loi[] = "Đơn giá thuê/m² phải là số dương!";
}

// Nếu có lỗi → redirect về form sửa kèm thông báo lỗi
if (!empty($loi)) {
    $thongBaoLoi = urlencode(implode(' | ', $loi));
    header("Location: phong_sua.php?id=" . urlencode($maPhong) . "&loi=$thongBaoLoi");
    exit();
}

// ── Bước 3: Kiểm tra phòng có thực sự tồn tại trong DB không ─
$maPhong_esc = $conn->real_escape_string($maPhong);
$sql_kt = "SELECT maPhong FROM PHONG WHERE maPhong = '$maPhong_esc'";
$res_kt = $conn->query($sql_kt);

if (!$res_kt || $res_kt->num_rows === 0) {
    header("Location: phong_hienthi.php?loi=" . urlencode("Phòng '$maPhong' không tồn tại!"));
    exit();
}

// ── Bước 4: Lấy heSoGia từ DB để tính lại giaThue (server-side) ──
// Không tin dữ liệu từ client – luôn tính lại từ DB
$maTang_esc = $conn->real_escape_string($maTang);
$sql_tang   = "SELECT heSoGia FROM TANG WHERE maTang = '$maTang_esc'";
$res_tang   = $conn->query($sql_tang);

if (!$res_tang || $res_tang->num_rows === 0) {
    header("Location: phong_sua.php?id=" . urlencode($maPhong) . "&loi=" . urlencode("Tầng đã chọn không hợp lệ!"));
    exit();
}

$tang_row = $res_tang->fetch_assoc();
$heSoGia  = (float)$tang_row['heSoGia'];

// Tính giá thuê theo công thức nghiệp vụ (server-side)
$giaThue = round((float)$donGiaM2 * (float)$dienTich * $heSoGia, 0);

// ── Bước 5: UPDATE vào CSDL ──────────────────────────────────
$moTaViTri_esc = $conn->real_escape_string($moTaViTri);
$trangThai_esc = $conn->real_escape_string($trangThai);

$sql_update = "UPDATE PHONG SET
                   maTang       = '$maTang_esc',
                   dienTich     = $dienTich,
                   soChoLamViec = $soChoLamViec,
                   donGiaM2     = $donGiaM2,
                   giaThue      = $giaThue,
                   moTaViTri    = '$moTaViTri_esc',
                   trangThai    = '$trangThai_esc'
               WHERE maPhong = '$maPhong_esc'";

if ($conn->query($sql_update) === TRUE) {
    // ✅ Thành công → chuyển về danh sách kèm thông báo
    $tb = urlencode("Cập nhật phòng '$maPhong' thành công! Giá thuê mới: " . number_format($giaThue, 0, ',', '.') . " ₫/tháng");
    header("Location: phong_hienthi.php?thanhcong=$tb");
    exit();
} else {
    // ❌ Lỗi DB → quay về form sửa kèm thông báo lỗi
    $loi_db = urlencode("Lỗi cơ sở dữ liệu: " . $conn->error);
    header("Location: phong_sua.php?id=" . urlencode($maPhong) . "&loi=$loi_db");
    exit();
}

$conn->close();
?>