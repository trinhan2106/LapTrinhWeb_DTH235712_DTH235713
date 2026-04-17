<?php
/**
 * RESET_PASS.PHP – Script tiện ích để đồng bộ lại mật khẩu Bcrypt cho toàn bộ Database
 */
require_once "cauhinh.php";

// 1. Tạo mã băm Bcrypt chuẩn cho mật khẩu "123456"
$newHash = password_hash('123456', PASSWORD_DEFAULT);

echo "<h3>Hệ thống đang cập nhật mật khẩu...</h3>";

// 2. Cập nhật bảng NHAN_VIEN
$sql_nv = "UPDATE NHAN_VIEN SET matKhau = '$newHash'";
if ($conn->query($sql_nv)) {
    echo "<p style='color:green;'>- Đã cập nhật xong mật khẩu cho bảng NHAN_VIEN.</p>";
} else {
    echo "<p style='color:red;'>- Lỗi cập nhật NHAN_VIEN: " . $conn->error . "</p>";
}

// 3. Cập nhật bảng KHACH_HANG
$sql_kh = "UPDATE KHACH_HANG SET matKhau = '$newHash'";
if ($conn->query($sql_kh)) {
    echo "<p style='color:green;'>- Đã cập nhật xong mật khẩu cho bảng KHACH_HANG.</p>";
} else {
    echo "<p style='color:red;'>- Lỗi cập nhật KHACH_HANG: " . $conn->error . "</p>";
}

// 4. Thông báo hoàn tất bằng chữ to
echo "<hr>";
echo "<h1 style='color:blue; font-family: sans-serif;'>Đã cập nhật mật khẩu Bcrypt (123456) thành công cho toàn bộ tài khoản!</h1>";
echo "<p>Bây giờ bạn có thể quay lại trang <a href='dangnhap.php'>Đăng nhập</a> để test.</p>";

// Đóng kết nối
$conn->close();
?>
