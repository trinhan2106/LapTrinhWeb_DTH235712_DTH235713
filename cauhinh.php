<?php
/**
 * CAUHINH.PHP – Kết nối Cơ sở dữ liệu
 * Web Hệ thống Quản lý Vận hành Cho thuê Cao ốc
 */

// Thiết lập báo lỗi MySQLi chuẩn xác (ném Exception thay vì chỉ hiện Warning)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Thiết lập mã hóa cho output trình duyệt
header("Content-type: text/html; charset=utf-8");

// Định nghĩa hằng số BASE_URL động để tránh lỗi 404 khi chạy trong thư mục con
$folder_name = 'LapTrinhWeb_DTH235712_DTH235713';
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/' . $folder_name);

// ============================================================
// CẤU HÌNH KẾT NỐI DATABASE
// ============================================================
$servername = "localhost";
$username   = "root";
$password   = ""; 
$dbname     = "quan_ly_cao_oc";

try {
    // TẠO KẾT NỐI (MySQLi Hướng Đối Tượng)
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Đặt charset utf8mb4 (hỗ trợ đầy đủ tiếng Việt và biểu tượng hiện đại)
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    // Nếu lỗi kết nối → dừng chương trình và hiện thông báo bảo mật
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

// Nếu không có lỗi → $conn đã sẵn sàng
