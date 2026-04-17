<?php
/**
 * ADMIN/KHACH_HANG/KH_SUA.PHP – Form cập nhật thông tin khách hàng
 */
require_once "../../thuvien.php";
require_once "../../cauhinh.php";
kiemTraSession();

$id = trim($_GET['id'] ?? '');
$id_esc = chongSQLInjection($conn, $id);

// Lấy dữ liệu cũ
$sql = "SELECT * FROM KHACH_HANG WHERE maKH = '$id_esc'";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    header("Location: kh_hienthi.php?loi=" . urlencode("Không tìm thấy khách hàng!"));
    exit();
}
$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập Nhật Khách Hàng | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>CẬP NHẬT THÔNG TIN</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($_GET['loi'])): ?>
                        <div class="alert alert-danger small mb-4"><?php echo e($_GET['loi']); ?></div>
                    <?php endif; ?>

                    <form action="kh_sua_submit.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mã Khách Hàng (Không được sửa)</label>
                            <input type="text" name="maKH" class="form-control bg-light" value="<?php echo e($data['maKH']); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên Khách Hàng / Công ty</label>
                            <input type="text" name="tenKH" class="form-control" value="<?php echo e($data['tenKH']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số Điện Thoại</label>
                            <input type="text" name="soDienThoai" class="form-control" value="<?php echo e($data['soDienThoai']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo e($data['email']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Địa chỉ</label>
                            <input type="text" name="diaChi" class="form-control" value="<?php echo e($data['diaChi']); ?>">
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">Lưu cập nhật</button>
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
