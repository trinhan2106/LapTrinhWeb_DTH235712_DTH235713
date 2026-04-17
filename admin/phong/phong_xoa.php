<?php
/**
 * PHONG_XOA.PHP – Xóa phòng
 *
 * FILE NÀY KHÔNG CÓ HTML.
 * Luồng chạy:
 *   1. Nhận ?id= từ URL
 *   2. Kiểm tra phòng có tồn tại không
 *   3. Kiểm tra RÀNG BUỘC: phòng đang cho thuê (trangThai = 'DangThue')
 *                           HOẶC đã có trong CHI_TIET_HOP_DONG → KHÔNG cho xóa
 *   4. Nếu an toàn → DELETE → redirect về danh sách
 *
 * Nguyên tắc bảo toàn dữ liệu:
 *   - Không bao giờ xóa phòng đang thuê
 *   - Không bao giờ xóa phòng đã từng xuất hiện trong hợp đồng
 *     (vì hóa đơn, chi tiết hợp đồng vẫn còn tham chiếu đến phòng đó)
 */

// ── Bước 0: Bảo vệ – chỉ nhận GET (xóa qua link có confirm JS) ──
require_once "../../thuvien.php";
kiemTraSession();

require_once "../../cauhinh.php";

// ── Bước 1: Lấy mã phòng từ URL ──────────────────────────────
$maPhong = trim($_GET['id'] ?? '');

if (empty($maPhong)) {
    header("Location: phong_hienthi.php?loi=" . urlencode("Thiếu mã phòng cần xóa!"));
    exit();
}

$maPhong_esc = $conn->real_escape_string($maPhong);

// ── Bước 2: Kiểm tra phòng có tồn tại không ─────────────────
$sql_kt_ton_tai = "SELECT maPhong, trangThai FROM PHONG WHERE maPhong = '$maPhong_esc'";
$res_ton_tai    = $conn->query($sql_kt_ton_tai);

if (!$res_ton_tai || $res_ton_tai->num_rows === 0) {
    header("Location: phong_hienthi.php?loi=" . urlencode("Không tìm thấy phòng '$maPhong'!"));
    exit();
}

$phong_hientai = $res_ton_tai->fetch_assoc();

// ── Bước 3: Kiểm tra ràng buộc #1 – Phòng đang cho thuê ─────
if ($phong_hientai['trangThai'] === 'DangThue') {
    $loi_thuê = urlencode("Không thể xóa phòng '$maPhong' vì phòng đang có người thuê! Hãy kết thúc hợp đồng trước.");
    header("Location: phong_hienthi.php?loi=$loi_thuê");
    exit();
}

// ── Bước 4: Kiểm tra ràng buộc #2 – Đã từng có trong hợp đồng ──
// Nếu phòng đã xuất hiện trong CHI_TIET_HOP_DONG (dù đã kết thúc),
// không cho xóa vì lịch sử hợp đồng vẫn cần tham chiếu đến phòng đó.
$sql_kt_hopdong = "SELECT COUNT(*) AS soLanXuat FROM CHI_TIET_HOP_DONG WHERE maPhong = '$maPhong_esc'";
$res_hopdong    = $conn->query($sql_kt_hopdong);

if ($res_hopdong) {
    $row_hd = $res_hopdong->fetch_assoc();
    if ((int)$row_hd['soLanXuat'] > 0) {
        $loi_hd = urlencode("Không thể xóa phòng '$maPhong' vì phòng này đã từng được đưa vào hợp đồng ({$row_hd['soLanXuat']} lần). Dữ liệu lịch sử cần được bảo toàn.");
        header("Location: phong_hienthi.php?loi=$loi_hd");
        exit();
    }
}

// ── Bước 5: Kiểm tra ràng buộc #3 – Có bản ghi chỉ số điện/nước ──
$sql_kt_diennuoc = "SELECT COUNT(*) AS soLan FROM CHI_SO_DIEN_NUOC WHERE maPhong = '$maPhong_esc'";
$res_diennuoc    = $conn->query($sql_kt_diennuoc);

if ($res_diennuoc) {
    $row_dn = $res_diennuoc->fetch_assoc();
    if ((int)$row_dn['soLan'] > 0) {
        $loi_dn = urlencode("Không thể xóa phòng '$maPhong' vì đã có {$row_dn['soLan']} bản ghi chỉ số điện/nước liên quan.");
        header("Location: phong_hienthi.php?loi=$loi_dn");
        exit();
    }
}

// ── Bước 6: Đủ điều kiện → thực hiện DELETE ──────────────────
$sql_xoa = "DELETE FROM PHONG WHERE maPhong = '$maPhong_esc'";

if ($conn->query($sql_xoa) === TRUE) {
    // ✅ Xóa thành công
    $tb = urlencode("Đã xóa phòng '$maPhong' thành công!");
    header("Location: phong_hienthi.php?thanhcong=$tb");
    exit();
} else {
    // ❌ Lỗi DB khi xóa
    $loi_db = urlencode("Lỗi khi xóa phòng '$maPhong': " . $conn->error);
    header("Location: phong_hienthi.php?loi=$loi_db");
    exit();
}

$conn->close();

