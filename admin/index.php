<?php
/**
 * ADMIN/INDEX.PHP – Trang chủ quản trị (Dashboard)
 */
require_once "../thuvien.php";
require_once "../cauhinh.php";
kiemTraSession();

// ── TRUY VẤN 1: Tổng số phòng đang TRỐNG ──────────
$res1 = $conn->query("SELECT COUNT(*) AS total FROM PHONG WHERE trangThai = 'Trống'");
$tongPhongTrong = $res1 ? $res1->fetch_assoc()['total'] : 0;

// ── TRUY VẤN 2: Tổng số khách hàng ──────────
$res2 = $conn->query("SELECT COUNT(*) AS total FROM KHACH_HANG");
$tongKhachHang = $res2 ? $res2->fetch_assoc()['total'] : 0;

// ── TRUY VẤN 3: Tổng số tiền còn NỢ (Kế toán) ──────────
$res3 = $conn->query("SELECT SUM(soTienConNo) AS total FROM HOA_DON");
$tongNo = $res3 ? $res3->fetch_assoc()['total'] : 0;

// ── TRUY VẤN 4: Top 5 Hợp đồng mới nhất (JOIN) ──────────
$sql_top5 = "SELECT h.soHopDong, h.ngayHieuLuc, k.tenKH, h.trangThai
             FROM HOP_DONG h
             JOIN KHACH_HANG k ON h.maKH = k.maKH
             ORDER BY h.ngayHieuLuc DESC
             LIMIT 5";
$dsTop5 = $conn->query($sql_top5);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ Thống Quản Trị | Office Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .stat-card { transition: all .3s; border: none; border-radius: 12px; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,.1); }
        .stat-icon { font-size: 2.5rem; opacity: 0.3; }
    </style>
</head>
<body class="bg-light">

<!-- Navbar Đơn giản -->
<nav class="navbar navbar-dark bg-dark shadow-sm py-3 mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1"><i class="bi bi-building me-2"></i>ADMIN DASHBOARD</span>
        <div class="d-flex align-items-center">
            <span class="text-white small me-3">Xin chào, <strong><?php echo e($_SESSION['tenNV']); ?></strong></span>
            <a href="<?php echo BASE_URL; ?>/admin/dangxuat.php" class="btn btn-outline-light btn-sm">Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">
    
    <!-- Hàng Card Thống kê -->
    <div class="row g-4 mb-5">
        
        <div class="col-md-4">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase small fw-bold">Phòng đang trống</h6>
                        <h2 class="mb-0 fw-bold"><?php echo $tongPhongTrong; ?></h2>
                    </div>
                    <i class="bi bi-door-open stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase small fw-bold">Tổng số khách hàng</h6>
                        <h2 class="mb-0 fw-bold"><?php echo $tongKhachHang; ?></h2>
                    </div>
                    <i class="bi bi-people stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card bg-danger text-white h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase small fw-bold">Tổng tiền nợ (Billing)</h6>
                        <h2 class="mb-0 fw-bold"><?php echo dinhDangTien($tongNo); ?></h2>
                    </div>
                    <i class="bi bi-cash-stack stat-icon"></i>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4">
        
        <!-- Top 5 Hợp đồng -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-file-earmark-text me-2"></i>5 Hợp đồng mới nhất</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small">
                                <tr>
                                    <th class="ps-3">Số HD</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($dsTop5 && $dsTop5->num_rows > 0): ?>
                                    <?php while ($h = $dsTop5->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold"><?php echo e($h['soHopDong']); ?></td>
                                        <td><?php echo e($h['tenKH']); ?></td>
                                        <td><?php echo dinhDangNgay($h['ngayHieuLuc']); ?></td>
                                        <td><?php echo badgeTrangThaiHD($h['trangThai']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-4">Chưa có hợp đồng nào.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu nhanh -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-lightning-charge me-2"></i>Thao tác nhanh</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo BASE_URL; ?>/admin/khach_hang/kh_hienthi.php" class="list-group-item list-group-item-action py-3">
                             <i class="bi bi-people me-3 text-primary"></i>Quản lý khách hàng
                        </a>
                        <a href="<?php echo BASE_URL; ?>/admin/nhan_vien/nv_hienthi.php" class="list-group-item list-group-item-action py-3">
                             <i class="bi bi-person-badge me-3 text-info"></i>Quản lý nhân viên
                        </a>
                        <a href="<?php echo BASE_URL; ?>/admin/thong_bao_thanh_toan.php" class="list-group-item list-group-item-action py-3 bg-light">
                             <i class="bi bi-bell-fill me-3 text-danger pulse"></i>Thông báo thu phí (UC05)
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

</body>
</html>
