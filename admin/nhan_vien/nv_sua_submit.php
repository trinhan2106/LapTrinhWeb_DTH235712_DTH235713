<?php
/**
 * ADMIN/NHAN_VIEN/NV_SUA_SUBMIT.PHP – Xử lý cập nhật nhân viên 
 */
require_once "../../thuvien.php";
require_once "../../cauhinh.php";
kiemTraSession();
kiemTraQuyen(1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: nv_hienthi.php");
    exit();
}

// 1. Nhận và làm sạch dữ liệu
$maNV         = trim($_POST['maNV'] ?? '');
$tenNV        = trim($_POST['tenNV'] ?? '');
$tenDangNhap  = trim($_POST['tenDangNhap'] ?? '');
$matKhauMoi   = $_POST['matKhau'] ?? '';
$chucVu       = trim($_POST['chucVu'] ?? '');
$boPhan       = trim($_POST['boPhan'] ?? '');
$soDienThoai  = trim($_POST['soDienThoai'] ?? '');
$quyenHan     = (int)($_POST['quyenHan'] ?? 3);
$dangLamViec  = (int)($_POST['dangLamViec'] ?? 1);

$maNV_esc         = chongSQLInjection($conn, $maNV);
$tenNV_esc        = chongSQLInjection($conn, $tenNV);
$tenDangNhap_esc  = chongSQLInjection($conn, $tenDangNhap);
$chucVu_esc       = chongSQLInjection($conn, $chucVu);
$boPhan_esc       = chongSQLInjection($conn, $boPhan);
$soDienThoai_esc  = chongSQLInjection($conn, $soDienThoai);

// 2. Validate cơ bản
if (empty($maNV) || empty($tenNV) || empty($tenDangNhap)) {
    header("Location: nv_sua.php?id=$maNV&loi=" . urlencode("Không được để trống Tên và Username!"));
    exit();
}

// 3. Xây dựng câu lệnh SQL Update
$sql = "UPDATE NHAN_VIEN SET 
            tenNV        = '$tenNV_esc',
            tenDangNhap  = '$tenDangNhap_esc',
            chucVu       = '$chucVu_esc',
            boPhan       = '$boPhan_esc',
            soDienThoai  = '$soDienThoai_esc',
            quyenHan     = $quyenHan,
            dangLamViec  = $dangLamViec";

// Chỉ cập nhật mật khẩu nếu người dùng có nhập mật khẩu mới
if (!empty($matKhauMoi)) {
    $matKhau_hash = password_hash($matKhauMoi, PASSWORD_BCRYPT);
    $matKhau_hash_esc = chongSQLInjection($conn, $matKhau_hash);
    $sql .= ", matKhau = '$matKhau_hash_esc'";
}

$sql .= " WHERE maNV = '$maNV_esc'";

if ($conn->query($sql) === TRUE) {
    header("Location: nv_hienthi.php?thanhcong=" . urlencode("Cập nhật nhân viên '$tenNV' thành công!"));
    exit();
} else {
    header("Location: nv_sua.php?id=$maNV&loi=" . urlencode("Lỗi CSDL: " . $conn->error));
    exit();
}
