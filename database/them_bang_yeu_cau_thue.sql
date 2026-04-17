-- ============================================================
-- THEM_BANG_YEU_CAU_THUE.SQL
-- Bổ sung bảng YEU_CAU_THUE vào database đã có
--
-- Chạy file này TRONG phpMyAdmin sau khi đã import file
-- quan_ly_cao_oc.sql chính.
--
-- Tác giả: Trần Trí Nhân (DTH235712)
-- ============================================================

USE quan_ly_cao_oc;

-- ────────────────────────────────────────────────────────────
-- BẢNG 12: YEU_CAU_THUE
-- Lưu yêu cầu đăng ký thuê từ khách vãng lai (trang public)
-- Bảng này KHÔNG có FK bắt buộc sang PHONG vì:
--   - Khách có thể nhập sai mã phòng
--   - Admin cần xem rồi mới duyệt thủ công
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS YEU_CAU_THUE (
    maYeuCau     INT          AUTO_INCREMENT PRIMARY KEY    COMMENT 'Mã yêu cầu (tự tăng)',
    hoTen        VARCHAR(100) NOT NULL                      COMMENT 'Họ tên người/công ty liên hệ',
    soDienThoai  VARCHAR(15)  NOT NULL                      COMMENT 'Số điện thoại liên hệ',
    email        VARCHAR(100) DEFAULT NULL                  COMMENT 'Email liên hệ (tùy chọn)',
    diaChi       VARCHAR(200) DEFAULT NULL                  COMMENT 'Địa chỉ hiện tại của khách',
    maPhong      CHAR(10)     DEFAULT NULL                  COMMENT 'Mã phòng khách muốn thuê',
    ngayBatDau   DATE         DEFAULT NULL                  COMMENT 'Ngày dự kiến bắt đầu thuê',
    thoiGianThue INT          DEFAULT 6                     COMMENT 'Thời gian thuê dự kiến (tháng, tối thiểu 6)',
    ghiChu       TEXT         DEFAULT NULL                  COMMENT 'Ghi chú thêm của khách',
    trangThai    VARCHAR(20)  DEFAULT 'ChoDuyet'            COMMENT 'ChoDuyet / DaDuyet / TuChoi',
    ngayGui      DATETIME     DEFAULT CURRENT_TIMESTAMP     COMMENT 'Ngày giờ gửi yêu cầu (tự ghi)',

    -- Index để tìm kiếm nhanh theo trạng thái và số điện thoại
    INDEX idx_trangThai    (trangThai),
    INDEX idx_soDienThoai  (soDienThoai),
    INDEX idx_maPhong      (maPhong)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Yêu cầu đăng ký thuê phòng từ khách vãng lai';


-- ────────────────────────────────────────────────────────────
-- DỮ LIỆU MẪU (cho việc test giao diện admin)
-- ────────────────────────────────────────────────────────────
INSERT INTO YEU_CAU_THUE
    (hoTen, soDienThoai, email, diaChi, maPhong, ngayBatDau, thoiGianThue, ghiChu, trangThai)
VALUES
    ('Công ty TNHH Phát Triển Xanh', '0909111222', 'phatrienxanh@gmail.com',
     '45 Điện Biên Phủ, Q.3, TP.HCM', 'P-00001',
     DATE_ADD(CURDATE(), INTERVAL 7 DAY), 12,
     'Cần phòng có view đẹp, gần thang máy', 'ChoDuyet'),

    ('Nguyễn Văn Bình', '0908333444', 'nguyenbinh@outlook.com',
     '12 Lê Lợi, Q.1, TP.HCM', 'P-00003',
     DATE_ADD(CURDATE(), INTERVAL 14 DAY), 6,
     'Ưu tiên tầng cao', 'ChoDuyet'),

    ('Startup ABC', '0977555666', 'hello@startabc.vn',
     '789 Cách Mạng Tháng 8, Q.10', 'P-00002',
     DATE_ADD(CURDATE(), INTERVAL 30 DAY), 24,
     'Cần ít nhất 20 chỗ ngồi, có phòng họp riêng', 'DaDuyet');
