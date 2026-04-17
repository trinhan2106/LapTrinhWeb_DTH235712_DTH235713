<?php
/**
 * ADMIN/KHACH_HANG/KH_THEM.PHP – Form thêm khách hàng mới
 */
require_once "../../thuvien.php";
require_once "../../cauhinh.php";
kiemTraSession();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Khách Hàng | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-success"><i class="bi bi-person-plus me-2"></i>THÊM KHÁCH HÀNG MỚI</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($_GET['loi'])): ?>
                        <div class="alert alert-danger small mb-4"><?php echo e($_GET['loi']); ?></div>
                    <?php endif; ?>

                    <form action="kh_them_submit.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mã Khách Hàng</label>
                            <input type="text" name="maKH" class="form-control" placeholder="Ví dụ: KH001" required autofocus>
                            <div class="form-text">Mã định danh duy nhất (không trùng lặp).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên Khách Hàng / Công ty</label>
                            <input type="text" name="tenKH" class="form-control" placeholder="Nhập tên đầy đủ" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số Điện Thoại</label>
                            <input type="text" name="soDienThoai" class="form-control" placeholder="Ví dụ: 0909123456" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="ten@congty.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Địa chỉ</label>
                            <input type="text" name="diaChi" class="form-control" placeholder="Số nhà, Tên đường, Quận/Huyện">
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4">Lưu thông tin</button>
                            <a href="kh_hienthi.php" class="btn btn-outline-secondary px-4">Hủy bỏ</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
