<?php
/**
 * ADMIN/NHAN_VIEN/NV_SUA.PHP – Form sửa thông tin nhân viên
 */
require_once "../../thuvien.php";
require_once "../../cauhinh.php";
kiemTraSession();
kiemTraQuyen(1);

$id = trim($_GET['id'] ?? '');
$id_esc = chongSQLInjection($conn, $id);

// Lấy dữ liệu cũ
$sql = "SELECT * FROM NHAN_VIEN WHERE maNV = '$id_esc'";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    header("Location: nv_hienthi.php?loi=" . urlencode("Không tìm thấy nhân viên!"));
    exit();
}
$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Nhân Viên | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-gear me-2"></i>CẬP NHẬT NHÂN VIÊN</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($_GET['loi'])): ?>
                        <div class="alert alert-danger small mb-4"><?php echo e($_GET['loi']); ?></div>
                    <?php endif; ?>

                    <form action="nv_sua_submit.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Mã Nhân Viên (Không được sửa)</label>
                                <input type="text" name="maNV" class="form-control bg-light" value="<?php echo e($data['maNV']); ?>" readonly>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Họ và Tên</label>
                                <input type="text" name="tenNV" class="form-control" value="<?php echo e($data['tenNV']); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tên đăng nhập (Username)</label>
                                <input type="text" name="tenDangNhap" class="form-control" value="<?php echo e($data['tenDangNhap']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-danger">Mật khẩu mới (Để trống nếu không đổi)</label>
                                <input type="password" name="matKhau" class="form-control" placeholder="Để trống để giữ nguyên">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Chức vụ</label>
                                <input type="text" name="chucVu" class="form-control" value="<?php echo e($data['chucVu']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bộ phận</label>
                                <input type="text" name="boPhan" class="form-control" value="<?php echo e($data['boPhan']); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại</label>
                                <input type="text" name="soDienThoai" class="form-control" value="<?php echo e($data['soDienThoai']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Quyền hạn</label>
                                <select name="quyenHan" class="form-select">
                                    <option value="3" <?php echo ($data['quyenHan'] == 3) ? 'selected' : ''; ?>>Nhân viên</option>
                                    <option value="2" <?php echo ($data['quyenHan'] == 2) ? 'selected' : ''; ?>>Quản lý</option>
                                    <option value="1" <?php echo ($data['quyenHan'] == 1) ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <select name="dangLamViec" class="form-select">
                                    <option value="1" <?php echo ($data['dangLamViec'] == 1) ? 'selected' : ''; ?>>Đang làm việc</option>
                                    <option value="0" <?php echo ($data['dangLamViec'] == 0) ? 'selected' : ''; ?>>Đã nghỉ việc</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">Lưu thay đổi</button>
                            <a href="nv_hienthi.php" class="btn btn-outline-secondary px-4">Quay lại</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
