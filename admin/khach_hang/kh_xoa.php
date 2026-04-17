<?php
/**
 * ADMIN/KHACH_HANG/KH_XOA.PHP – Xử lý xóa khách hàng (Có kiểm tra ràng buộc)
 */
require_once "../../thuvien.php";
require_once "../../cauhinh.php";
kiemTraSession();

$id = trim($_GET['id'] ?? '');
$id_esc = chongSQLInjection($conn, $id);

if (empty($id)) {
    header("Location: kh_hienthi.php");
    exit();
}

// 1. KIỂM TRA RÀNG BUỘC: Nếu đã có Hợp đồng thì không cho xóa
$sql_kt = "SELECT soHopDong FROM HOP_DONG WHERE maKH = '$id_esc' LIMIT 1";
$res_kt = $conn->query($sql_kt);

if ($res_kt && $res_kt->num_rows > 0) {
    // Đã có dữ liệu liên quan ở bảng HOP_DONG
    header("Location: kh_hienthi.php?loi=" . urlencode("Không thể xóa Khách hàng này vì đã tồn tại Hợp đồng liên quan (Dữ liệu kế toán)!"));
    exit();
}

// 2. Chấp nhận xóa nếu không có ràng buộc
$sql = "DELETE FROM KHACH_HANG WHERE maKH = '$id_esc'";

if ($conn->query($sql) === TRUE) {
    header("Location: kh_hienthi.php?thanhcong=" . urlencode("Xóa khách hàng thành công!"));
    exit();
} else {
    header("Location: kh_hienthi.php?loi=" . urlencode("Lỗi khi xóa: " . $conn->error));
    exit();
}
