<?php
/**
 * ADMIN/KHACH_HANG/KH_SUA_SUBMIT.PHP – Xử lý cập nhật khách hàng
 */
require_once "../../thuvien.php";
require_once "../../cauhinh.php";
kiemTraSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: kh_hienthi.php");
    exit();
}

// 1. Nhận và làm sạch dữ liệu
$maKH        = trim($_POST['maKH'] ?? '');
$tenKH       = trim($_POST['tenKH'] ?? '');
$soDienThoai = trim($_POST['soDienThoai'] ?? '');
$email       = trim($_POST['email'] ?? '');
$diaChi      = trim($_POST['diaChi'] ?? '');

$maKH_esc        = chongSQLInjection($conn, $maKH);
$tenKH_esc       = chongSQLInjection($conn, $tenKH);
$soDienThoai_esc = chongSQLInjection($conn, $soDienThoai);
$email_esc       = chongSQLInjection($conn, $email);
$diaChi_esc      = chongSQLInjection($conn, $diaChi);

// 2. Kiểm tra nghiệp vụ
if (empty($maKH) || empty($tenKH) || empty($soDienThoai)) {
    header("Location: kh_sua.php?id=$maKH&loi=" . urlencode("Vui lòng nhập đầy đủ các trường bắt buộc!"));
    exit();
}

// 3. Thực hiện UPDATE
$sql = "UPDATE KHACH_HANG 
        SET tenKH = '$tenKH_esc', soDienThoai = '$soDienThoai_esc', email = '$email_esc', diaChi = '$diaChi_esc' 
        WHERE maKH = '$maKH_esc'";

if ($conn->query($sql) === TRUE) {
    header("Location: kh_hienthi.php?thanhcong=" . urlencode("Cập nhật thông tin thành công!"));
    exit();
} else {
    header("Location: kh_sua.php?id=$maKH&loi=" . urlencode("Lỗi CSDL: " . $conn->error));
    exit();
}
