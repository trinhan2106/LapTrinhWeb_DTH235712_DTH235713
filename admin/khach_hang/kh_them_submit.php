<?php
/**
 * ADMIN/KHACH_HANG/KH_THEM_SUBMIT.PHP – Xử lý thêm khách hàng
 */
require_once "../../thuvien.php";
require_once "../../cauhinh.php";
kiemTraSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: kh_hienthi.php");
    exit();
}

// 1. Nhận và làm sạch dữ liệu
$maKH        = strtoupper(trim($_POST['maKH'] ?? ''));
$tenKH       = trim($_POST['tenKH'] ?? '');
$soDienThoai = trim($_POST['soDienThoai'] ?? '');
$email       = trim($_POST['email'] ?? '');
$diaChi      = trim($_POST['diaChi'] ?? '');

$maKH_esc        = chongSQLInjection($conn, $maKH);
$tenKH_esc       = chongSQLInjection($conn, $tenKH);
$soDienThoai_esc = chongSQLInjection($conn, $soDienThoai);
$email_esc       = chongSQLInjection($conn, $email);
$diaChi_esc      = chongSQLInjection($conn, $diaChi);

// 2. Kiểm tra nghiệp vụ cơ bản
if (empty($maKH) || empty($tenKH) || empty($soDienThoai)) {
    header("Location: kh_them.php?loi=" . urlencode("Vui lòng nhập đầy đủ các trường bắt buộc!"));
    exit();
}

// 3. Kiểm tra trùng mã KH
$sql_kt = "SELECT maKH FROM KHACH_HANG WHERE maKH = '$maKH_esc'";
$res_kt = $conn->query($sql_kt);
if ($res_kt && $res_kt->num_rows > 0) {
    header("Location: kh_them.php?loi=" . urlencode("Mã khách hàng '$maKH' đã tồn tại!"));
    exit();
}

// 4. Thực hiện INSERT
$sql = "INSERT INTO KHACH_HANG (maKH, tenKH, soDienThoai, email, diaChi, ngayDangKy) 
        VALUES ('$maKH_esc', '$tenKH_esc', '$soDienThoai_esc', '$email_esc', '$diaChi_esc', CURDATE())";

if ($conn->query($sql) === TRUE) {
    header("Location: kh_hienthi.php?thanhcong=" . urlencode("Thêm khách hàng thành công!"));
    exit();
} else {
    header("Location: kh_them.php?loi=" . urlencode("Lỗi CSDL: " . $conn->error));
    exit();
}
