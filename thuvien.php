<?php
/**
 * THUVIEN.PHP – Các hàm xử lý dùng chung
 * Web Hệ thống Quản lý Vận hành Cho thuê Cao ốc
 *
 * CÁCH DÙNG: Thêm dòng này vào ĐẦU mọi file cần dùng:
 *   require_once "../thuvien.php";
 */

// ============================================================
// NHÓM 1: ĐỊNH DẠNG DỮ LIỆU
// ============================================================

/**
 * Định dạng ngày từ Y-m-d (lưu trong DB) sang d/m/Y (hiển thị)
 * Ví dụ: "2026-03-21" → "21/03/2026"
 */
function dinhDangNgay($ngay) {
    if (empty($ngay) || $ngay === '0000-00-00') {
        return '---';
    }
    return date('d/m/Y', strtotime($ngay));
}

/**
 * Định dạng số tiền theo kiểu Việt Nam
 * Ví dụ: 1500000 → "1.500.000 ₫"
 */
function dinhDangTien($soTien) {
    return number_format((float)$soTien, 0, ',', '.') . ' ₫';
}

/**
 * Escape HTML để chống tấn công XSS
 * Dùng khi in dữ liệu từ DB ra giao diện
 * Ví dụ: echo e($row['tenKH']);
 */
function e($chuoi) {
    return htmlspecialchars($chuoi ?? '', ENT_QUOTES, 'UTF-8');
}

// ============================================================
// NHÓM 2: KIỂM TRA SESSION & PHÂN QUYỀN
// ============================================================

/**
 * Kiểm tra người dùng đã đăng nhập chưa.
 * Nếu chưa → tự động chuyển hướng về trang đăng nhập.
 *
 * GỌI HÀM NÀY Ở ĐẦU MỌI TRANG CẦN BẢO VỆ.
 *
 * Ví dụ sử dụng:
 *   require_once "../thuvien.php";
 *   kiemTraSession();
 */
function kiemTraSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['maNV']) || empty($_SESSION['maNV'])) {
        header("Location: ../dangnhap.php");
        exit();
    }
}

/**
 * Kiểm tra quyền truy cập cụ thể.
 * Nếu không đúng quyền → hiện thông báo lỗi và dừng.
 *
 * @param string|array $quyen  Quyền cần có. Ví dụ: 1 (Admin), 2 (Quản lý Nhà)
 *
 * Ví dụ sử dụng:
 *   kiemTraQuyen([1, 2]); // Chỉ Admin và Quản lý Nhà mới vào được
 */
function kiemTraQuyen($quyen) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $quyenHienTai = $_SESSION['quyenHan'] ?? 0;
    $danhSachQuyen = is_array($quyen) ? $quyen : [$quyen];

    if (!in_array($quyenHienTai, $danhSachQuyen)) {
        http_response_code(403);
        die('<div class="alert alert-danger m-3">
                ⛔ Bạn không có quyền truy cập chức năng này!
                <a href="javascript:history.back()">← Quay lại</a>
             </div>');
    }
}

// ============================================================
// NHÓM 3: NGHIỆP VỤ TÍNH TOÁN
// ============================================================

/**
 * Tính giá thuê phòng theo công thức nghiệp vụ (FIELD CHỈ ĐỌC)
 * giaThue = donGiaM2 × dienTich × heSoGia
 *
 * Kết quả làm tròn đến đơn vị đồng (0 số thập phân)
 */
function tinhGiaThue($donGiaM2, $dienTich, $heSoGia) {
    return round((float)$donGiaM2 * (float)$dienTich * (float)$heSoGia, 0);
}

/**
 * Tính số tiền phải nộp thực tế (có bù trừ nợ kỳ trước)
 *
 * Công thức nghiệp vụ:
 *   soTienPhaiNop = tongTien + soTienConNo_kyTruoc
 *
 *   Nếu soTienConNo_kyTruoc < 0 (dư tiền) → giảm bớt số phải nộp
 *   Nếu soTienConNo_kyTruoc > 0 (còn nợ) → cộng thêm vào
 *
 * @param float $tongGiaThang    Tổng giá thuê các phòng trong 1 tháng
 * @param float $soNoKyTruoc     Số tiền còn nợ kỳ trước (âm=dư, dương=nợ)
 * @param int   $soKy            Số kỳ thanh toán (kỳ 1 = 6 tháng gộp, kỳ ≥ 2 = 1 tháng)
 * @return float
 */
function tinhTienPhaiNop($tongGiaThang, $soNoKyTruoc, $soKy) {
    $tongTien = ($soKy == 1) ? (float)$tongGiaThang * 6 : (float)$tongGiaThang;
    return $tongTien + (float)$soNoKyTruoc;
}

/**
 * Lấy số tiền còn nợ kỳ gần nhất của một hợp đồng từ DB
 *
 * @param mysqli $conn          Kết nối CSDL
 * @param string $soHopDong     Số hợp đồng cần tra
 * @return float  Số tiền còn nợ (âm = dư, dương = còn nợ, 0 = sạch nợ)
 */
function layNoKyTruoc($conn, $soHopDong) {
    $soHD = $conn->real_escape_string($soHopDong);
    $sql  = "SELECT soTienConNo FROM HOA_DON
             WHERE soHopDong = '$soHD'
             ORDER BY ngayThanhToan DESC, kyThanhToan DESC
             LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return (float)$row['soTienConNo'];
    }
    return 0.0; // Chưa có hóa đơn nào → không có nợ
}

// ============================================================
// NHÓM 4: HIỂN THỊ GIAO DIỆN (BADGE BOOTSTRAP)
// ============================================================

/**
 * Hiển thị badge Bootstrap cho trạng thái phòng
 */
function badgeTrangThaiPhong($trangThai) {
    switch ($trangThai) {
        case 'Trống':    return '<span class="badge bg-success">🟢 Trống</span>';
        case 'DangThue': return '<span class="badge bg-danger">🔴 Đang thuê</span>';
        case 'BaoTri':   return '<span class="badge bg-warning text-dark">🟡 Bảo trì</span>';
        default:         return '<span class="badge bg-secondary">' . e($trangThai) . '</span>';
    }
}

/**
 * Hiển thị badge Bootstrap cho trạng thái hợp đồng
 */
function badgeTrangThaiHD($trangThai) {
    switch ($trangThai) {
        case 'DangHieuLuc': return '<span class="badge bg-success">Đang hiệu lực</span>';
        case 'GiaHan':      return '<span class="badge bg-info text-dark">Đã gia hạn</span>';
        case 'DaHuy':       return '<span class="badge bg-danger">Đã hủy</span>';
        case 'HetHan':      return '<span class="badge bg-secondary">Hết hạn</span>';
        default:            return '<span class="badge bg-secondary">' . e($trangThai) . '</span>';
    }
}

/**
 * Hiển thị badge Bootstrap cho trạng thái hóa đơn
 */
function badgeTrangThaiHD_HoaDon($trangThai) {
    switch ($trangThai) {
        case 'DaThu':  return '<span class="badge bg-success">✅ Đã thu</span>';
        case 'ConNo':  return '<span class="badge bg-danger">⚠️ Còn nợ</span>';
        default:       return '<span class="badge bg-secondary">' . e($trangThai) . '</span>';
    }
}
?>