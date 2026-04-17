-- Khởi tạo Database nếu chưa có và chỉ định bảng mã hỗ trợ Tiếng Việt
CREATE DATABASE IF NOT EXISTS quan_ly_cao_oc 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE quan_ly_cao_oc;

-- ────────────────────────────────────────────────────────────
-- 1. BẢNG CAO_OC (Bảng cha cao nhất)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS CAO_OC (
    maCaoOc CHAR(10) PRIMARY KEY,
    tenCaoOc VARCHAR(100) NOT NULL,
    diaChi VARCHAR(200) NOT NULL,
    moTa TEXT NULL,
    -- Chuyển FLOAT sang DECIMAL(15,2) để đồng bộ phép tính chính xác
    tongDienTich DECIMAL(15,2) NOT NULL CHECK (tongDienTich >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 2. BẢNG TANG
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS TANG (
    maTang CHAR(10) PRIMARY KEY,
    soTang INT NOT NULL CHECK (soTang >= 1),
    heSoGia DECIMAL(5,2) NOT NULL DEFAULT 1.00 CHECK (heSoGia > 0),
    maCaoOc CHAR(10) NOT NULL,
    FOREIGN KEY (maCaoOc) REFERENCES CAO_OC(maCaoOc) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 3. BẢNG PHONG
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS PHONG (
    maPhong CHAR(10) PRIMARY KEY,
    -- Chuyển FLOAT sang DECIMAL(15,2) để tránh sai số làm tròn khi nhân đơn giá
    dienTich DECIMAL(15,2) NOT NULL CHECK (dienTich > 0),
    soChoLamViec INT NOT NULL CHECK (soChoLamViec > 0),
    moTaViTri VARCHAR(200) NULL,
    donGiaM2 DECIMAL(15,2) NOT NULL CHECK (donGiaM2 > 0),
    giaThue DECIMAL(15,2) DEFAULT 0,
    trangThai VARCHAR(20) DEFAULT "Trống",
    maTang CHAR(10) NOT NULL,
    FOREIGN KEY (maTang) REFERENCES TANG(maTang) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 4. BẢNG NHAN_VIEN
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS NHAN_VIEN (
    maNV CHAR(10) PRIMARY KEY,
    tenNV VARCHAR(100) NOT NULL,
    chucVu VARCHAR(50) NOT NULL,
    boPhan VARCHAR(50) NOT NULL,
    soDienThoai VARCHAR(15) NULL,
    dangLamViec TINYINT(1) DEFAULT 1,
    tenDangNhap VARCHAR(50) NULL,
    matKhau VARCHAR(255) NULL,
    quyenHan INT DEFAULT 1 COMMENT "1: Admin, 2: Quanly, 3: Nhanvien"
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 5. BẢNG KHACH_HANG
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS KHACH_HANG (
    maKH CHAR(10) PRIMARY KEY,
    tenKH VARCHAR(100) NOT NULL,
    diaChi VARCHAR(200) NULL,
    soDienThoai VARCHAR(15) NOT NULL,
    email VARCHAR(100) NULL,
    ngayDangKy DATE DEFAULT (CURRENT_DATE),
    tenDangNhap VARCHAR(50) UNIQUE,
    matKhau VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 6. BẢNG HOP_DONG
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS HOP_DONG (
    soHopDong CHAR(15) PRIMARY KEY,
    ngayHieuLuc DATE NOT NULL,
    ngayThanhToanDauTien DATE NOT NULL,
    -- Ràng buộc nghiệp vụ: Thuê tối thiểu 6 tháng
    thoiGianThue INT NOT NULL CHECK (thoiGianThue >= 6),
    ngayHetHanCuoiCung DATE NOT NULL,
    maKH CHAR(10) NOT NULL,
    trangThai VARCHAR(20) DEFAULT "DangHieuLuc",
    ngayHuy DATE NULL,
    lyDoHuy VARCHAR(200) NULL,
    FOREIGN KEY (maKH) REFERENCES KHACH_HANG(maKH) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 7. BẢNG CHI_TIET_HOP_DONG
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS CHI_TIET_HOP_DONG (
    soHopDong CHAR(15),
    maPhong CHAR(10),
    giaThue DECIMAL(15,2) NOT NULL CHECK (giaThue > 0),
    ngayBatDau DATE NOT NULL,
    ngayHetHan DATE NOT NULL,
    trangThai VARCHAR(20) DEFAULT "DangThue",
    PRIMARY KEY (soHopDong, maPhong),
    FOREIGN KEY (soHopDong) REFERENCES HOP_DONG(soHopDong) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (maPhong) REFERENCES PHONG(maPhong) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 8. BẢNG GIA_HAN_HOP_DONG
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS GIA_HAN_HOP_DONG (
    soGiaHan CHAR(15) PRIMARY KEY,
    soHopDong CHAR(15) NOT NULL,
    ngayKyGiaHan DATE NOT NULL,
    moTa TEXT NULL,
    FOREIGN KEY (soHopDong) REFERENCES HOP_DONG(soHopDong) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 9. BẢNG CHI_TIET_GIA_HAN
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS CHI_TIET_GIA_HAN (
    soGiaHan CHAR(15),
    maPhong CHAR(10),
    thoiGianGiaHan INT NOT NULL CHECK (thoiGianGiaHan > 0),
    PRIMARY KEY (soGiaHan, maPhong),
    FOREIGN KEY (soGiaHan) REFERENCES GIA_HAN_HOP_DONG(soGiaHan) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (maPhong) REFERENCES PHONG(maPhong) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 10. BẢNG HOA_DON
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS HOA_DON (
    soPhieu CHAR(15) PRIMARY KEY,
    ngayThanhToan DATE DEFAULT (CURRENT_DATE),
    lyDo VARCHAR(50) NOT NULL,
    soHopDong CHAR(15) NOT NULL,
    maNV CHAR(10) NOT NULL,
    kyThanhToan INT NOT NULL CHECK (kyThanhToan >= 1),
    tongTien DECIMAL(15,2) NOT NULL CHECK (tongTien > 0),
    soTienDaNop DECIMAL(15,2) NOT NULL DEFAULT 0,
    soTienConNo DECIMAL(15,2) NOT NULL DEFAULT 0,
    trangThai VARCHAR(20) DEFAULT "ConNo",
    FOREIGN KEY (soHopDong) REFERENCES HOP_DONG(soHopDong) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (maNV) REFERENCES NHAN_VIEN(maNV) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 11. BẢNG CHI_SO_DIEN_NUOC
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS CHI_SO_DIEN_NUOC (
    maChiSo CHAR(15) PRIMARY KEY,
    maPhong CHAR(10) NOT NULL,
    thangGhi INT NOT NULL CHECK (thangGhi BETWEEN 1 AND 12),
    namGhi INT NOT NULL CHECK (namGhi > 2000),
    chiSoDien_Dau DECIMAL(15,2) NOT NULL DEFAULT 0,
    chiSoDien_Cuoi DECIMAL(15,2) NOT NULL DEFAULT 0,
    chiSoNuoc_Dau DECIMAL(15,2) NOT NULL DEFAULT 0,
    chiSoNuoc_Cuoi DECIMAL(15,2) NOT NULL DEFAULT 0,
    donGiaDien DECIMAL(15,2) NOT NULL,
    donGiaNuoc DECIMAL(15,2) NOT NULL,
    ngayGhi DATE DEFAULT (CURRENT_DATE),
    -- Ràng buộc nghiệp vụ: Mỗi phòng chỉ ghi một lần mỗi tháng
    UNIQUE (maPhong, thangGhi, namGhi),
    FOREIGN KEY (maPhong) REFERENCES PHONG(maPhong) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 12. BẢNG YEU_CAU_THUE (Dành cho trang Public Khách Hàng)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS YEU_CAU_THUE (
    maYeuCau     INT          AUTO_INCREMENT PRIMARY KEY    COMMENT "Mã yêu cầu (tự tăng)",
    hoTen        VARCHAR(100) NOT NULL                      COMMENT "Họ tên người/công ty liên hệ",
    soDienThoai  VARCHAR(15)  NOT NULL                      COMMENT "Số điện thoại liên hệ",
    email        VARCHAR(100) DEFAULT NULL                  COMMENT "Email liên hệ (tùy chọn)",
    diaChi       VARCHAR(200) DEFAULT NULL                  COMMENT "Địa chỉ hiện tại của khách",
    maPhong      CHAR(10)     DEFAULT NULL                  COMMENT "Mã phòng khách muốn thuê",
    ngayBatDau   DATE         DEFAULT NULL                  COMMENT "Ngày dự kiến bắt đầu thuê",
    thoiGianThue INT          DEFAULT 6                     COMMENT "Thời gian thuê dự kiến (tháng, tối thiểu 6)",
    ghiChu       TEXT         DEFAULT NULL                  COMMENT "Ghi chú thêm của khách",
    trangThai    VARCHAR(20)  DEFAULT "ChoDuyet"            COMMENT "ChoDuyet / DaDuyet / TuChoi",
    ngayGui      DATETIME     DEFAULT CURRENT_TIMESTAMP     COMMENT "Ngày giờ gửi yêu cầu (tự ghi)",

    INDEX idx_trangThai    (trangThai),
    INDEX idx_soDienThoai  (soDienThoai),
    INDEX idx_maPhong      (maPhong)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- TỐI ƯU INDEX (TĂNG TỐC BÁO CÁO)
-- ────────────────────────────────────────────────────────────
CREATE INDEX idx_phong_trangThai ON PHONG(trangThai);
CREATE INDEX idx_hopdong_trangThai ON HOP_DONG(trangThai);
CREATE INDEX idx_hopdong_ngayHetHan ON HOP_DONG(ngayHetHanCuoiCung);

-- ────────────────────────────────────────────────────────────
-- DỮ LIỆU MẪU (DÀNH CHO TESTING - MIN 2 CO, 4 TANG, 8 PHONG, 3 NV)
-- ────────────────────────────────────────────────────────────

-- 1. CAO_OC
INSERT INTO CAO_OC (maCaoOc, tenCaoOc, diaChi, moTa, tongDienTich) VALUES
("CO001", "Bitexco Financial Tower", "2 Hải Triều, Q.1, TP.HCM", "Tòa nhà búp sen biểu tượng", 6000.00),
("CO002", "Landmark 81", "208 Nguyễn Hữu Cảnh, Q.Bình Thạnh", "Tòa nhà cao nhất Việt Nam", 12000.00);

-- 2. TANG (Min 4 tầng)
INSERT INTO TANG (maTang, soTang, heSoGia, maCaoOc) VALUES
("T01_CO1", 10, 1.20, "CO001"),
("T02_CO1", 20, 1.50, "CO001"),
("T01_CO2", 15, 1.30, "CO002"),
("T02_CO2", 25, 1.40, "CO002");

-- 3. PHONG (Min 8 phòng)
INSERT INTO PHONG (maPhong, dienTich, soChoLamViec, moTaViTri, donGiaM2, giaThue, trangThai, maTang) VALUES
("P1001", 150.00, 20, "Cạnh thang máy tầng 10", 500000.00, 90000000.00, "Trống", "T01_CO1"),
("P1002", 200.00, 30, "View sông Sài Gòn", 550000.00, 132000000.00, "Trống", "T01_CO1"),
("P1003", 80.00, 10, "Phòng studio tầng 10", 450000.00, 43200000.00, "Trống", "T01_CO1"),
("P2001", 100.00, 15, "Phía Tây tầng 20", 600000.00, 90000000.00, "Trống", "T02_CO1"),
("P2002", 120.00, 18, "Phía Đông tầng 20", 650000.00, 109200000.00, "Trống", "T02_CO1"),
("P3001", 300.00, 45, "Penthouse văn phòng", 700000.00, 252000000.00, "Trống", "T01_CO2"),
("P3002", 250.00, 35, "Văn phòng mở tầng 15", 680000.00, 204000000.00, "Trống", "T01_CO2"),
("P4001", 180.00, 25, "Hạng A tầng 25", 750000.00, 189000000.00, "Trống", "T02_CO2");

-- 4. NHAN_VIEN (Min 3 nhân viên - BCrypt password '123456')
INSERT INTO NHAN_VIEN (maNV, tenNV, chucVu, boPhan, soDienThoai, dangLamViec, tenDangNhap, matKhau, quyenHan) VALUES
("NV001", "Trần Phan Nhật", "Quản trị viên", "IT", "0901234567", 1, "admin", "$2y$10$8K1p/a0dxv.h8wPymO1/8.pT.rN7R7XGzO.fE0vR7D7lQoZ0f1S2q", 1),
("NV002", "Lê Văn Nhân", "Quản lý tòa nhà", "Vận hành", "0987654321", 1, "nhan_manager", "$2y$10$8K1p/a0dxv.h8wPymO1/8.pT.rN7R7XGzO.fE0vR7D7lQoZ0f1S2q", 2),
("NV003", "Nguyễn Thị Kế Toán", "Kế toán trưởng", "Tài chính", "0912223334", 1, "ketoan", "$2y$10$8K1p/a0dxv.h8wPymO1/8.pT.rN7R7XGzO.fE0vR7D7lQoZ0f1S2q", 3);

-- 5. KHACH_HANG (Min 2 khách hàng - BCrypt password '123456')
INSERT INTO KHACH_HANG (maKH, tenKH, diaChi, soDienThoai, email, ngayDangKy, tenDangNhap, matKhau) VALUES
("KH001", "Công ty Phần mềm ABC", "Q.3, TP.HCM", "0123456789", "contact@abc.com", "2026-01-10", "khachhang1", "$2y$10$8K1p/a0dxv.h8wPymO1/8.pT.rN7R7XGzO.fE0vR7D7lQoZ0f1S2q"),
("KH002", "Ngân hàng Thương mại XYZ", "Q.1, TP.HCM", "0988776655", "info@xyz.bank", "2026-02-15", "khachhang2", "$2y$10$8K1p/a0dxv.h8wPymO1/8.pT.rN7R7XGzO.fE0vR7D7lQoZ0f1S2q");

-- 6. HOP_DONG
INSERT INTO HOP_DONG (soHopDong, ngayHieuLuc, ngayThanhToanDauTien, thoiGianThue, ngayHetHanCuoiCung, maKH, trangThai) VALUES
("HD001", "2026-02-01", "2026-02-01", 12, "2027-02-01", "KH001", "DangHieuLuc");

-- 7. CHI_TIET_HOP_DONG
INSERT INTO CHI_TIET_HOP_DONG (soHopDong, maPhong, giaThue, ngayBatDau, ngayHetHan, trangThai) VALUES
("HD001", "P1002", 132000000.00, "2026-02-01", "2027-02-01", "DangThue");
UPDATE PHONG SET trangThai = "DangThue" WHERE maPhong = "P1002";

-- 10. HOA_DON
INSERT INTO HOA_DON (soPhieu, ngayThanhToan, lyDo, soHopDong, maNV, kyThanhToan, tongTien, soTienDaNop, soTienConNo, trangThai) VALUES
("HDN001", "2026-02-01", "Thanh toán cọc và kỳ 1", "HD001", "NV001", 1, 792000000.00, 792000000.00, 0, "DaThu");

-- 11. CHI_SO_DIEN_NUOC
INSERT INTO CHI_SO_DIEN_NUOC (maChiSo, maPhong, thangGhi, namGhi, chiSoDien_Dau, chiSoDien_Cuoi, chiSoNuoc_Dau, chiSoNuoc_Cuoi, donGiaDien, donGiaNuoc, ngayGhi) VALUES
("DN001", "P1002", 3, 2026, 1200.50, 1350.75, 50.00, 58.50, 3500.00, 15000.00, "2026-03-31");

-- 12. YEU_CAU_THUE
INSERT INTO YEU_CAU_THUE (hoTen, soDienThoai, email, diaChi, maPhong, ngayBatDau, thoiGianThue, ghiChu, trangThai) VALUES
("Nguyễn Hoàng Nam", "0911223344", "namnh@gmail.com", "Q.7, TP.HCM", "P1001", "2026-05-01", 12, "Cần thuê gấp", "ChoDuyet");