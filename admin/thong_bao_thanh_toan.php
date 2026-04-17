<?php
/**
 * ADMIN/THONG_BAO_THANH_TOAN.PHP – Danh sách Hợp đồng đến hạn (Kế toán)
 */
require_once "../thuvien.php";
require_once "../cauhinh.php";
kiemTraSession();

// Truy vấn các hợp đồng đang hiệu lực
// JOIN với KHACH_HANG để lấy tên
$sql = "SELECT h.soHopDong, h.ngayHieuLuc, h.ngayThanhToanDauTien, h.maKH, k.tenKH, h.thoiGianThue
        FROM HOP_DONG h
        JOIN KHACH_HANG k ON h.maKH = k.maKH
        WHERE h.trangThai = 'DangHieuLuc'
        ORDER BY h.ngayHieuLuc ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông Báo Thanh Toán | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .due-badge { animation: pulse 2s infinite; }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    
    <div class="d-flex align-items-center mb-4">
        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="btn btn-outline-secondary btn-sm me-3"><i class="bi bi-arrow-left"></i></a>
        <h4 class="mb-0 fw-bold text-danger"><i class="bi bi-bell-fill me-2"></i>HỢP ĐỒNG ĐẾN HẠN THANH TOÁN</h4>
    </div>

    <div class="alert alert-info border-0 shadow-sm small mb-4">
        <i class="bi bi-info-circle-fill me-2"></i>
        Hệ thống liệt kê các <strong>Hợp đồng đang hiệu lực</strong>. Kế toán cần kiểm tra kỳ thanh toán tiếp theo và bấm <strong>"Lập hóa đơn"</strong> để thực hiện thu phí.
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="small text-uppercase fw-bold text-muted">
                            <th class="ps-3 py-3">Số Hợp Đồng</th>
                            <th>Tên Khách Hàng</th>
                            <th>Ngày Ký</th>
                            <th>Kỳ Đầu Tiên</th>
                            <th>Tình trạng</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-primary"><?php echo e($row['soHopDong']); ?></td>
                                    <td>
                                        <div class="fw-semibold"><?php echo e($row['tenKH']); ?></div>
                                        <div class="text-muted small">Mã: <?php echo e($row['maKH']); ?></div>
                                    </td>
                                    <td><?php echo dinhDangNgay($row['ngayHieuLuc']); ?></td>
                                    <td><?php echo dinhDangNgay($row['ngayThanhToanDauTien']); ?></td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-normal px-2 py-1 due-badge">
                                            Đến kỳ thu phí
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo BASE_URL; ?>/admin/thanh_toan/tt_tao.php?soHopDong=<?php echo urlencode($row['soHopDong']); ?>" class="btn btn-danger btn-sm px-3">
                                            <i class="bi bi-cash-coin me-1"></i>Lập hóa đơn
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-check fs-1 d-block mb-3 opacity-25"></i>
                                    Không có hợp đồng nào đang hiệu lực cần xử lý.
                                </td>
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
