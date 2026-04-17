<?php
/**
 * ADMIN/NHAN_VIEN/NV_THEM_SUBMIT.PHP – Xử lý thêm nhân viên (Có mã hóa mật khẩu)
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
$maNV         = strtoupper(trim($_POST['maNV'] ?? ''));
$tenNV        = trim($_POST['tenNV'] ?? '');
$tenDangNhap  = trim($_POST['tenDangNhap'] ?? '');
$matKhau      = $_POST['matKhau'] ?? '';
$chucVu       = trim($_POST['chucVu'] ?? '');
$boPhan       = trim($_POST['boPhan'] ?? '');
$soDienThoai  = trim($_POST['soDienThoai'] ?? '');
$quyenHan     = (int)($_POST['quyenHan'] ?? 3);

$maNV_esc        = chongSQLInjection($conn, $maNV);
$tenNV_esc       = chongSQLInjection($conn, $tenNV);
$tenDangNhap_esc = chongSQLInjection($conn, $tenDangNhap);
$chucVu_esc       = chongSQLInjection($conn, $chucVu);
$boPhan_esc       = chongSQLInjection($conn, $boPhan);
$soDienThoai_esc = chongSQLInjection($conn, $soDienThoai);

// 2. Validate
if (empty($maNV) || empty($tenNV) || empty($tenDangNhap) || empty($matKhau)) {
    header("Location: nv_them.php?loi=" . urlencode("Vui lòng điền đầy đủ các thông tin quan trọng!"));
    exit();
}

// 3. Kiểm tra trùng mã NV hoặc trùng Username
$sql_kt = "SELECT maNV FROM NHAN_VIEN WHERE maNV = '$maNV_esc' OR tenDangNhap = '$tenDangNhap_esc' LIMIT 1";
$res_kt = $conn->query($sql_kt);
if ($res_kt && $res_kt->num_rows > 0) {
    header("Location: nv_them.php?loi=" . urlencode("Mã nhân viên hoặc Tên đăng nhập đã tồn tại trong hệ thống!"));
    exit();
}

// 4. Mã hóa mật khẩu (Bcrypt)
$matKhau_hash = password_hash($matKhau, PASSWORD_BCRYPT);
$matKhau_hash_esc = chongSQLInjection($conn, $matKhau_hash);

// 5. Thực hiện INSERT
$sql = "INSERT INTO NHAN_VIEN (maNV, tenNV, chucVu, boPhan, soDienThoai, tenDangNhap, matKhau, quyenHan, dangLamViec) 
        VALUES ('$maNV_esc', '$tenNV_esc', '$chucVu_esc', '$boPhan_esc', '$soDienThoai_esc', '$tenDangNhap_esc', '$matKhau_hash_esc', $quyenHan, 1)";

if ($conn->query($sql) === TRUE) {
    header("Location: nv_hienthi.php?thanhcong=" . urlencode("Thêm nhân viên '$tenNV' thành công!"));
    exit();
} else {
    header("Location: nv_them.php?loi=" . urlencode("Lỗi CSDL: " . $conn->error));
    exit();
}
