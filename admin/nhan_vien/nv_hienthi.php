<?php
/**
 * ADMIN/NHAN_VIEN/NV_HIENTHI.PHP – Danh sách nhân viên
 */
require_once "../../thuvien.php";
require_once "../../cauhinh.php";
kiemTraSession();
kiemTraQuyen(1); // Chỉ Admin mới xem được danh sách nhân viên

$search = trim($_GET['search'] ?? '');
$search_esc = chongSQLInjection($conn, $search);

// Truy vấn lấy danh sách nhân viên
$sql = "SELECT maNV, tenNV, chucVu, boPhan, soDienThoai, dangLamViec, tenDangNhap, quyenHan FROM NHAN_VIEN";
if (!empty($search)) {
    $sql .= " WHERE tenNV LIKE '%$search_esc%' OR soDienThoai LIKE '%$search_esc%' OR tenDangNhap LIKE '%$search_esc%'";
}
$sql .= " ORDER BY maNV ASC";
$result = $conn->query($sql);

/**
 * Hiển thị nhãn quyền hạn
 */
function getQuyenHanText($level) {
    switch ($level) {
        case 1: return '<span class="badge bg-danger">Admin</span>';
        case 2: return '<span class="badge bg-warning text-dark">Quản lý</span>';
        case 3: return '<span class="badge bg-info text-dark">Nhân viên</span>';
        default: return '<span class="badge bg-secondary">Khách</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý nhân viên | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-badge me-2"></i>QUẢN LÝ NHÂN VIÊN</h5>
            <a href="nv_them.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Thêm Nhân Viên</a>
        </div>
        <div class="card-body">
            
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, SĐT hoặc username..." value="<?php echo e($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Tìm</button>
                </div>
            </form>

            <?php if (isset($_GET['thanhcong'])): ?>
                <div class="alert alert-success small"><?php echo e($_GET['thanhcong']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['loi'])): ?>
                <div class="alert alert-danger small"><?php echo e($_GET['loi']); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-light">
                        <tr>
                            <th>Mã NV</th>
                            <th>Họ Tên</th>
                            <th>Username</th>
                            <th>Chức vụ</th>
                            <th>Quyền</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo e($row['maNV']); ?></td>
                                    <td><?php echo e($row['tenNV']); ?></td>
                                    <td><code><?php echo e($row['tenDangNhap']); ?></code></td>
                                    <td><?php echo e($row['chucVu']); ?> (<?php echo e($row['boPhan']); ?>)</td>
                                    <td><?php echo getQuyenHanText($row['quyenHan']); ?></td>
                                    <td>
                                        <?php if ($row['dangLamViec']): ?>
                                            <span class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>Đang làm</span>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="bi bi-x-circle-fill me-1"></i>Đã nghỉ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="nv_sua.php?id=<?php echo urlencode($row['maNV']); ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="nv_xoa.php?id=<?php echo urlencode($row['maNV']); ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-4">Không có dữ liệu.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
