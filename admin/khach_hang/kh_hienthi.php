<?php
/**
 * ADMIN/KHACH_HANG/KH_HIENTHI.PHP – Danh sách khách hàng
 */
require_once "../../thuvien.php";
require_once "../../cauhinh.php";
kiemTraSession();

$search = trim($_GET['search'] ?? '');
$search_esc = chongSQLInjection($conn, $search);

// Truy vấn lấy danh sách khách hàng có lọc theo tên hoặc SĐT
$sql = "SELECT * FROM KHACH_HANG";
if (!empty($search)) {
    $sql .= " WHERE tenKH LIKE '%$search_esc%' OR soDienThoai LIKE '%$search_esc%'";
}
$sql .= " ORDER BY ngayDangKy DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý khách hàng | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-people me-2"></i>QUẢN LÝ KHÁCH HÀNG</h5>
            <a href="kh_them.php" class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i>Thêm Khách Hàng</a>
        </div>
        <div class="card-body">
            
            <!-- Form Tìm kiếm -->
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo tên hoặc số điện thoại..." value="<?php echo e($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Tìm kiếm</button>
                </div>
                <?php if ($search): ?>
                <div class="col-md-2">
                    <a href="kh_hienthi.php" class="btn btn-outline-secondary w-100">Xóa lọc</a>
                </div>
                <?php endif; ?>
            </form>

            <?php if (isset($_GET['thanhcong'])): ?>
                <div class="alert alert-success mt-3 small"><?php echo e($_GET['thanhcong']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['loi'])): ?>
                <div class="alert alert-danger mt-3 small"><?php echo e($_GET['loi']); ?></div>
            <?php endif; ?>

            <!-- Bảng dữ liệu -->
            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-light">
                        <tr>
                            <th>Mã KH</th>
                            <th>Tên Khách Hàng</th>
                            <th>Số Điện Thoại</th>
                            <th>Email</th>
                            <th>Ngày Đăng Ký</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo e($row['maKH']); ?></td>
                                    <td><?php echo e($row['tenKH']); ?></td>
                                    <td><?php echo e($row['soDienThoai']); ?></td>
                                    <td><?php echo e($row['email']); ?></td>
                                    <td><?php echo dinhDangNgay($row['ngayDangKy']); ?></td>
                                    <td class="text-center">
                                        <a href="kh_sua.php?id=<?php echo urlencode($row['maKH']); ?>" class="btn btn-outline-primary btn-sm me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="kh_xoa.php?id=<?php echo urlencode($row['maKH']); ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Không tìm thấy khách hàng nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
