<?php
/**
 * DANGNHAP_SUBMIT.PHP – Xử lý logic đăng nhập hợp nhất (Đã chuyển ra Root)
 */
session_start();
require_once "cauhinh.php";
require_once "thuvien.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "/dangnhap.php");
    exit();
}

// 1. Nhận dữ liệu từ form
$user = trim($_POST['tenDangNhap'] ?? '');
$pass = trim($_POST['matKhau']    ?? '');

// 2. Làm sạch dữ liệu đầu vào (Chống SQL Injection)
$user_esc = chongSQLInjection($conn, $user);

// 3. BƯỚC 1: KIỂM TRA TRONG BẢNG NHÂN VIÊN
try {
    // KHÔNG đưa mật khẩu vào WHERE khi dùng Bcrypt
    $sql_nv = "SELECT maNV, tenNV, matKhau, quyenHan FROM NHAN_VIEN 
               WHERE tenDangNhap = '$user_esc' AND dangLamViec = 1 LIMIT 1";
    $res_nv = $conn->query($sql_nv);

    if ($res_nv && $res_nv->num_rows > 0) {
        $row = $res_nv->fetch_assoc();
        
        // Kiểm tra mật khẩu bằng password_verify (Bcrypt)
        if (password_verify($pass, $row['matKhau'])) {
            // Đăng nhập thành công với tư cách NHÂN VIÊN
            $_SESSION['maNV']     = $row['maNV'];
            $_SESSION['tenNV']    = $row['tenNV'];
            $_SESSION['quyenHan'] = (int)$row['quyenHan']; // 1: Admin, 2: Quanly, 3: Nhanvien

            // Chuyển hướng vào khu vực quản trị
            header("Location: " . BASE_URL . "/admin/index.php"); 
            exit();
        }
    }

    // 4. BƯỚC 2: KIỂM TRA TRONG BẢNG KHÁCH HÀNG (Nếu nhân viên không khớp)
    $sql_kh = "SELECT maKH, tenKH, matKhau FROM KHACH_HANG 
               WHERE tenDangNhap = '$user_esc' LIMIT 1";
    $res_kh = $conn->query($sql_kh);

    if ($res_kh && $res_kh->num_rows > 0) {
        $row = $res_kh->fetch_assoc();

        if (password_verify($pass, $row['matKhau'])) {
            // Đăng nhập thành công với tư cách KHÁCH HÀNG
            $_SESSION['maKH']     = $row['maKH'];
            $_SESSION['tenKH']    = $row['tenKH'];
            $_SESSION['quyenHan'] = 0; // Quy ước 0 là Khách hàng

            // Chuyển hướng về Trang chủ Công cộng
            header("Location: " . BASE_URL . "/index.php");
            exit();
        }
    }

    // Nếu chạy đến đây tức là không khớp bất kỳ tài khoản nào
    $loi = "Tên đăng nhập hoặc mật khẩu không chính xác!";

} catch (Exception $e) {
    $loi = "Lỗi hệ thống: " . $e->getMessage();
}

// Quay lại trang login nếu thất bại
header("Location: " . BASE_URL . "/dangnhap.php?loi=" . urlencode($loi));
exit();
