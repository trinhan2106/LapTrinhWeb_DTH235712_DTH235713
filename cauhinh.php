<?php
/**
 * CAUHINH.PHP – Kết nối Cơ sở dữ liệu
 * Web Hệ thống Quản lý Vận hành Cho thuê Cao ốc
 *
 * HƯỚNG DẪN: Đây là file DUY NHẤT chứa thông tin kết nối.
 * Tất cả các file khác chỉ cần: require_once "../cauhinh.php";
 */

header("Content-type: text/html; charset=utf-8");

// ============================================================
// CẤU HÌNH KẾT NỐI DATABASE
// ============================================================
$servername = "localhost";
$username   = "root";
$password   = "vertrigo";       // XAMPP: đổi thành "" | Vertrigo: "vertrigo"
$dbname     = "quan_ly_cao_oc"; // Tên database đã tạo trong phpMyAdmin

// ============================================================
// TẠO KẾT NỐI (MySQLi Hướng Đối Tượng)
// ============================================================
$conn = new mysqli($servername, $username, $password, $dbname);

// Đặt charset UTF-8 (hỗ trợ tiếng Việt)
$conn->set_charset("utf8");

// Kiểm tra kết nối – nếu lỗi thì dừng chương trình và hiện thông báo
if ($conn->connect_error) {
    die("❌ Kết nối thất bại: " . $conn->connect_error);
    exit();
}
// Nếu không có lỗi → $conn đã sẵn sàng để dùng ở các file khác
?>