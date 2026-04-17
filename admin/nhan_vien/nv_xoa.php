<?php
/**
 * ADMIN/NHAN_VIEN/NV_XOA.PHP – Xử lý xóa nhân viên (Có kiểm tra ràng buộc)
 */
require_once "../../thuvien.php";
require_once "../../cauhinh.php";
kiemTraSession();
kiemTraQuyen(1);

$id = trim($_GET['id'] ?? '');
$id_esc = chongSQLInjection($conn, $id);

if (empty($id)) {
    header("Location: nv_hienthi.php");
    exit();
}

// 1. KIỂM TRA RÀNG BUỘC: Nếu nhân viên đã lập Hóa Đơn thì không cho xóa
$sql_kt = "SELECT soPhieu FROM HOA_DON WHERE maNV = '$id_esc' LIMIT 1";
$res_kt = $conn->query($sql_kt);

if ($res_kt && $res_kt->num_rows > 0) {
    header("Location: nv_hienthi.php?loi=" . urlencode("Không thể xóa Nhân viên này vì đã tham gia lập Hóa đơn (Dữ liệu kế toán)! Bạn có thể chuyển sang trạng thái 'Đã nghỉ việc' thay vì xóa."));
    exit();
}

// 2. Thực hiện xóa
$sql = "DELETE FROM NHAN_VIEN WHERE maNV = '$id_esc'";
if ($conn->query($sql) === TRUE) {
    header("Location: nv_hienthi.php?thanhcong=" . urlencode("Xóa nhân viên thành công!"));
    exit();
} else {
    header("Location: nv_hienthi.php?loi=" . urlencode("Lỗi khi xóa: " . $conn->error));
    exit();
}
