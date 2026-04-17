<?php
/**
 * ADMIN/NHAN_VIEN/NV_THEM.PHP – Form thêm nhân viên
 */
require_once "../../thuvien.php";
require_once "../../cauhinh.php";
kiemTraSession();
kiemTraQuyen(1); 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Nhân Viên | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-plus me-2"></i>THÊM NHÂN VIÊN MỚI</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($_GET['loi'])): ?>
                        <div class="alert alert-danger small mb-4"><?php echo e($_GET['loi']); ?></div>
                    <?php endif; ?>

                    <form action="nv_them_submit.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Mã Nhân Viên</label>
                                <input type="text" name="maNV" class="form-control" placeholder="NV001" required autofocus>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Họ và Tên</label>
                                <input type="text" name="tenNV" class="form-control" placeholder="Nguyễn Văn A" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tên đăng nhập (Username)</label>
                                <input type="text" name="tenDangNhap" class="form-control" placeholder="username" required>
                                <div class="form-text">Dùng để đăng nhập hệ thống.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mật khẩu</label>
                                <input type="password" name="matKhau" class="form-control" placeholder="********" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Chức vụ</label>
                                <input type="text" name="chucVu" class="form-control" placeholder="Ví dụ: Kế toán, Quản lý..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bộ phận</label>
                                <input type="text" name="boPhan" class="form-control" placeholder="Ví dụ: Tài chính, Vận hành..." required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại</label>
                                <input type="text" name="soDienThoai" class="form-control" placeholder="090...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Quyền hạn</label>
                                <select name="quyenHan" class="form-select">
                                    <option value="3">Nhân viên</option>
                                    <option value="2">Quản lý</option>
                                    <option value="1">Admin</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">Đăng ký nhân viên</button>
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
