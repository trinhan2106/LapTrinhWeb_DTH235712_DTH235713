<?php
/**
 * DANG_KY_THUE_SUBMIT.PHP – Xử lý logic gửi yêu cầu thuê phòng
 *
 * FILE NÀY KHÔNG CÓ HTML.
 *
 * Luồng chạy (theo chuẩn đồ án GV):
 *   1. Kiểm tra phương thức POST (chống truy cập trực tiếp qua GET)
 *   2. Kết nối CSDL
 *   3. Nhận dữ liệu từ $_POST
 *   4. VALIDATE: kiểm tra hợp lệ từng trường
 *   5. Nếu lỗi → header("Location: dang_ky_thue.php?loi=...") + exit()
 *   6. Làm sạch dữ liệu bằng real_escape_string() (chống SQL Injection)
 *   7. Kiểm tra phòng còn trống không (nghiệp vụ)
 *   8. INSERT vào bảng YEU_CAU_THUE
 *   9. Redirect về form kèm thông báo thành công
 *
 * GV hỏi: "Tại sao dùng real_escape_string() chứ không dùng printf/bind?"
 *   → Đây là đồ án PHP thuần, MySQLi hướng đối tượng, real_escape_string()
 *     là phương pháp chống SQLi phù hợp với cấp độ môn học.
 *     (Prepared Statement là nâng cao hơn, có thể giải thích thêm nếu GV hỏi)
 */

// ── Bước 0: Chỉ xử lý khi gửi form bằng POST ─────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Ai truy cập trực tiếp bằng GET → redirect về form
    header("Location: dang_ky_thue.php");
    exit();
}

// ── Bước 1: Kết nối CSDL ──────────────────────────────────────
require_once "cauhinh.php"; // Khởi tạo biến $conn (MySQLi)

// ── Bước 2: Nhận dữ liệu từ form ($_POST) ─────────────────────
// trim() loại bỏ khoảng trắng đầu/cuối người dùng vô tình nhập
$hoTen       = trim($_POST['hoTen']       ?? '');
$soDienThoai = trim($_POST['soDienThoai'] ?? '');
$email       = trim($_POST['email']       ?? '');
$diaChi      = trim($_POST['diaChi']      ?? '');
$maPhong     = trim($_POST['maPhong']     ?? '');
$ngayBatDau  = trim($_POST['ngayBatDau']  ?? '');
$thoiGianThue = (int)($_POST['thoiGianThue'] ?? 0);
$ghiChu      = trim($_POST['ghiChu']      ?? '');

// ── Bước 3: VALIDATE dữ liệu ──────────────────────────────────
$loi = []; // Mảng tổng hợp các lỗi phát hiện được

// Kiểm tra Họ tên
if (empty($hoTen)) {
    $loi[] = "Họ và tên không được để trống!";
} elseif (strlen($hoTen) > 100) {
    $loi[] = "Họ và tên không được vượt quá 100 ký tự!";
}

// Kiểm tra Số điện thoại (format Việt Nam: 9-11 chữ số)
if (empty($soDienThoai)) {
    $loi[] = "Số điện thoại không được để trống!";
} elseif (!preg_match('/^[0-9]{9,11}$/', $soDienThoai)) {
    $loi[] = "Số điện thoại không hợp lệ! Chỉ gồm 9–11 chữ số (VD: 0909123456).";
}

// Kiểm tra Email (nếu có nhập)
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $loi[] = "Địa chỉ email không đúng định dạng! (VD: ten@email.com)";
}
if (strlen($email) > 100) {
    $loi[] = "Email không được vượt quá 100 ký tự!";
}

// Kiểm tra Mã phòng
if (empty($maPhong)) {
    $loi[] = "Vui lòng chọn phòng muốn thuê!";
}

// Kiểm tra Ngày bắt đầu
if (empty($ngayBatDau)) {
    $loi[] = "Vui lòng chọn ngày dự kiến bắt đầu thuê!";
} else {
    // Ngày bắt đầu phải từ ngày mai trở đi
    $ngayMai = date('Y-m-d', strtotime('+1 day'));
    if ($ngayBatDau < $ngayMai) {
        $loi[] = "Ngày bắt đầu phải từ ngày mai (" . date('d/m/Y', strtotime($ngayMai)) . ") trở đi!";
    }
}

// Kiểm tra Thời gian thuê (ràng buộc nghiệp vụ: tối thiểu 6 tháng)
if ($thoiGianThue <= 0) {
    $loi[] = "Vui lòng chọn thời gian thuê dự kiến!";
} elseif ($thoiGianThue < 6) {
    $loi[] = "Thời gian thuê tối thiểu là 6 tháng theo quy định!";
}

// ── Bước 4: Nếu có lỗi → redirect về form kèm thông báo ──────
if (!empty($loi)) {
    // Ghép tất cả lỗi thành 1 chuỗi, encode URL
    $thongBaoLoi = urlencode(implode(' | ', $loi));
    header("Location: dang_ky_thue.php?loi=$thongBaoLoi");
    exit();
}

// ── Bước 5: Làm sạch dữ liệu (chống SQL Injection) ───────────
// real_escape_string() escape các ký tự đặc biệt trong chuỗi SQL:
// dấu nháy đơn ('), dấu nháy kép ("), backslash (\), NUL byte, v.v.
$hoTen_esc        = $conn->real_escape_string($hoTen);
$soDienThoai_esc  = $conn->real_escape_string($soDienThoai);
$email_esc        = $conn->real_escape_string($email);
$diaChi_esc       = $conn->real_escape_string($diaChi);
$maPhong_esc      = $conn->real_escape_string($maPhong);
$ngayBatDau_esc   = $conn->real_escape_string($ngayBatDau);
$ghiChu_esc       = $conn->real_escape_string($ghiChu);

// ── Bước 6: Kiểm tra phòng còn trống không (nghiệp vụ) ───────
// Phòng có thể đã được thuê sau khi khách mở form → cần kiểm tra lại
$sql_kt_phong = "SELECT maPhong, trangThai FROM PHONG WHERE maPhong = '$maPhong_esc' LIMIT 1";
$res_kt_phong = $conn->query($sql_kt_phong);

if (!$res_kt_phong || $res_kt_phong->num_rows === 0) {
    // Mã phòng không tồn tại trong DB
    $loi_phong = urlencode("Phòng '$maPhong' không tồn tại trong hệ thống!");
    header("Location: dang_ky_thue.php?loi=$loi_phong");
    exit();
}

$phong_row = $res_kt_phong->fetch_assoc();
if ($phong_row['trangThai'] !== 'Trống') {
    // Phòng đã được thuê trong lúc khách điền form
    $loi_phong = urlencode("Rất tiếc! Phòng '$maPhong' vừa được đặt thuê bởi người khác. Vui lòng chọn phòng khác.");
    header("Location: dang_ky_thue.php?loi=$loi_phong");
    exit();
}

// ── Bước 7: Kiểm tra đã có yêu cầu trùng lặp chưa ───────────
// Cùng SĐT + cùng phòng + trạng thái ChoDuyet → tránh spam
$sql_kt_trung = "SELECT maYeuCau FROM YEU_CAU_THUE
                 WHERE soDienThoai = '$soDienThoai_esc'
                   AND maPhong     = '$maPhong_esc'
                   AND trangThai   = 'ChoDuyet'
                 LIMIT 1";
$res_kt_trung = $conn->query($sql_kt_trung);

if ($res_kt_trung && $res_kt_trung->num_rows > 0) {
    $loi_trung = urlencode("Số điện thoại này đã có yêu cầu thuê phòng '$maPhong' đang chờ duyệt! Nhân viên sẽ liên hệ sớm.");
    header("Location: dang_ky_thue.php?loi=$loi_trung");
    exit();
}

// ── Bước 8: INSERT vào bảng YEU_CAU_THUE ─────────────────────
/*
 * Cấu trúc bảng YEU_CAU_THUE (đã tạo trong file SQL):
 *   maYeuCau    INT AUTO_INCREMENT PRIMARY KEY
 *   hoTen       VARCHAR(100) NOT NULL
 *   soDienThoai VARCHAR(15)  NOT NULL
 *   email       VARCHAR(100)
 *   diaChi      VARCHAR(200)
 *   maPhong     CHAR(10)
 *   ngayBatDau  DATE
 *   thoiGianThue INT DEFAULT 6
 *   ghiChu      TEXT
 *   trangThai   VARCHAR(20) DEFAULT 'ChoDuyet'
 *   ngayGui     DATETIME DEFAULT CURRENT_TIMESTAMP
 */
$sql_insert = "INSERT INTO YEU_CAU_THUE
                   (hoTen, soDienThoai, email, diaChi, maPhong, ngayBatDau, thoiGianThue, ghiChu, trangThai)
               VALUES
                   ('$hoTen_esc',
                    '$soDienThoai_esc',
                    '$email_esc',
                    '$diaChi_esc',
                    '$maPhong_esc',
                    '$ngayBatDau_esc',
                    $thoiGianThue,
                    '$ghiChu_esc',
                    'ChoDuyet')";

if ($conn->query($sql_insert) === TRUE) {
    // ✅ INSERT thành công
    // Tạo thông báo thành công rõ ràng cho khách
    $thanhcong = urlencode(
        "🎉 Gửi yêu cầu thành công! " .
        "Nhân viên tư vấn sẽ liên hệ số điện thoại " . $soDienThoai .
        " trong vòng 24 giờ để xác nhận. Cảm ơn bạn đã quan tâm đến phòng " . $maPhong . "!"
    );
    header("Location: dang_ky_thue.php?thanhcong=$thanhcong");
    exit();

} else {
    // ❌ Lỗi truy vấn DB
    $loi_db = urlencode("Lỗi hệ thống khi lưu yêu cầu. Vui lòng thử lại hoặc liên hệ hotline 0909 123 456!");
    header("Location: dang_ky_thue.php?loi=$loi_db");
    exit();
}

// Đóng kết nối (không thực sự chạy tới đây vì đã exit() ở trên)
$conn->close();

