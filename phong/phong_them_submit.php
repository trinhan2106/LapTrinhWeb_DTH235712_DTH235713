<?php
/**
 * PHONG_THEM_SUBMIT.PHP – Xử lý logic thêm phòng
 *
 * FILE NÀY KHÔNG CÓ HTML.
 * Luồng chạy:
 *   1. Nhận dữ liệu POST từ phong_them.php
 *   2. Validate (kiểm tra hợp lệ)
 *   3. Nếu lỗi → header("Location: phong_them.php?loi=...") rồi exit()
 *   4. Nếu OK  → INSERT vào DB → header("Location: phong_hienthi.php?thanhcong=...") rồi exit()
 *
 * GV hỏi: "Giải thích luồng chạy của file _submit.php?"
 *   → Đây chính là file để giải thích: nhận POST, validate, query DB, redirect.
 */

// ── Bảo vệ: chỉ nhận POST, không cho truy cập trực tiếp qua GET ──
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: phong_hienthi.php");
    exit();
}

// ── Kết nối CSDL ─────────────────────────────────────────────────
require_once "../cauhinh.php";

// ── Bước 1: Lấy dữ liệu từ FORM ($_POST) ────────────────────────
$maPhong      = trim($_POST['maPhong']      ?? '');
$maTang       = trim($_POST['maTang']       ?? '');
$dienTich     = trim($_POST['dienTich']     ?? '');
$soChoLamViec = trim($_POST['soChoLamViec'] ?? '');
$donGiaM2     = trim($_POST['donGiaM2']     ?? '');
$moTaViTri    = trim($_POST['moTaViTri']    ?? '');
$trangThai    = trim($_POST['trangThai']    ?? 'Trống');

// ── Bước 2: VALIDATE dữ liệu ────────────────────────────────────
$loi = []; // Mảng chứa các lỗi phát hiện

if (empty($maPhong)) {
    $loi[] = "Mã phòng không được để trống!";
} elseif (strlen($maPhong) > 10) {
    $loi[] = "Mã phòng không được vượt quá 10 ký tự!";
} elseif (!preg_match('/^[A-Za-z0-9\-_]+$/', $maPhong)) {
    $loi[] = "Mã phòng chỉ được chứa chữ cái, số, gạch ngang và gạch dưới!";
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

// Nếu có lỗi → redirect về form kèm thông báo lỗi
if (!empty($loi)) {
    $thongBaoLoi = urlencode(implode(' | ', $loi));
    header("Location: phong_them.php?loi=$thongBaoLoi");
    exit();
}

// ── Bước 3: Kiểm tra mã phòng đã tồn tại chưa ──────────────────
$maPhong_esc = $conn->real_escape_string($maPhong);
$sql_kt = "SELECT maPhong FROM PHONG WHERE maPhong = '$maPhong_esc'";
$res_kt = $conn->query($sql_kt);

if ($res_kt && $res_kt->num_rows > 0) {
    $loi_trung = urlencode("Mã phòng '$maPhong' đã tồn tại trong hệ thống! Vui lòng chọn mã khác.");
    header("Location: phong_them.php?loi=$loi_trung");
    exit();
}

// ── Bước 4: Lấy heSoGia từ DB để tính giaThue (không tin dữ liệu từ form) ──
$maTang_esc = $conn->real_escape_string($maTang);
$sql_tang = "SELECT heSoGia FROM TANG WHERE maTang = '$maTang_esc'";
$res_tang = $conn->query($sql_tang);

if (!$res_tang || $res_tang->num_rows == 0) {
    header("Location: phong_them.php?loi=" . urlencode("Tầng đã chọn không hợp lệ!"));
    exit();
}

$tang_row = $res_tang->fetch_assoc();
$heSoGia  = (float)$tang_row['heSoGia'];

// Tính giá thuê tự động theo công thức nghiệp vụ (server-side, không tin JS)
$giaThue = round((float)$donGiaM2 * (float)$dienTich * $heSoGia, 0);

// ── Bước 5: INSERT vào CSDL ────────────────────────────────────
$moTaViTri_esc = $conn->real_escape_string($moTaViTri);
$trangThai_esc = $conn->real_escape_string($trangThai);

$sql_insert = "INSERT INTO PHONG
                   (maPhong, dienTich, soChoLamViec, moTaViTri, donGiaM2, giaThue, trangThai, maTang)
               VALUES
                   ('$maPhong_esc',
                    $dienTich,
                    $soChoLamViec,
                    '$moTaViTri_esc',
                    $donGiaM2,
                    $giaThue,
                    '$trangThai_esc',
                    '$maTang_esc')";

if ($conn->query($sql_insert) === TRUE) {
    // ✅ Thành công → chuyển về danh sách kèm thông báo
    $tb = urlencode("Thêm phòng '$maPhong' thành công! Giá thuê: " . number_format($giaThue, 0, ',', '.') . " ₫/tháng");
    header("Location: phong_hienthi.php?thanhcong=$tb");
    exit();
} else {
    // ❌ Lỗi DB → chuyển về form kèm thông báo lỗi
    $loi_db = urlencode("Lỗi cơ sở dữ liệu: " . $conn->error);
    header("Location: phong_them.php?loi=$loi_db");
    exit();
}

$conn->close();
?>