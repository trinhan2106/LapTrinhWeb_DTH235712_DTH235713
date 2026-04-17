# 📋 BẢNG PHÂN CÔNG CÔNG VIỆC CHI TIẾT
## Đồ án: Web Hệ thống Quản lý Vận hành Cho thuê Cao ốc

| Thông tin | Chi tiết |
|-----------|----------|
| **Môn học** | Lập trình Web – Lớp DH23PM |
| **Giảng viên** | ThS. Thiều Thanh Quang Phú |
| **Nhóm** | Trần Trí Nhân (DTH235712) & Huỳnh Minh Nhật (DTH235713) |
| **Năm học** | 2025 – 2026 |
| **Tỉ lệ đóng góp** | 50% – 50% |

---

## 👤 THÀNH VIÊN 1: HUỲNH MINH NHẬT (DTH235713) – Front-end & Danh mục
**Phụ trách:** Giao diện Public, bộ khung `/includes/`, Module Phòng, Module Cao ốc/Tầng, Báo cáo

---

### 🗂️ NHIỆM VỤ 1: Bộ khung giao diện dùng chung (Public)

| # | File đường dẫn | Công việc cần code | Kỹ thuật sử dụng |
|---|---------------|-------------------|------------------|
| 1 | `/includes/header.php` | Viết `<!DOCTYPE html>` đến `<body>`, nhúng Bootstrap 5 CDN + Bootstrap Icons CDN, nhúng `assets/css/style.css`. Dùng biến `$pageTitle` (khai báo trước khi `include`) để render `<title>` động. | HTML, CSS Custom Properties (`:root`), Bootstrap 5 |
| 2 | `/includes/navbar.php` | Viết `<nav>` responsive dùng `navbar-expand-lg`. **Quan trọng:** Query DB lấy danh sách `tenCaoOc` từ bảng `CAO_OC` bằng vòng lặp `while + fetch_assoc()` để render Dropdown "Cao ốc" động (TUYỆT ĐỐI không hardcode tên cao ốc). Dùng hàm `isActive($pageName)` để đánh dấu menu active dựa trên biến `$currentPage`. | PHP, MySQLi, Bootstrap Navbar, Dropdown |
| 3 | `/includes/footer.php` | Viết footer 4 cột (Giới thiệu / Liên kết nhanh / Liên hệ / Hotline). In năm bản quyền bằng `date('Y')`. Nhúng Bootstrap JS Bundle CDN. Đóng `</body></html>`. | HTML, Bootstrap Grid |
| 4 | `/includes/banner.php` | Banner hero section có ảnh nền gradient. Dùng biến `$bannerTitle`, `$bannerSubtitle`, `$showSearch` (khai báo trước khi include) để tùy biến nội dung theo từng trang. Nếu `$showSearch = true` thì render form tìm kiếm nhanh inline. | PHP, Bootstrap Jumbotron/Hero |
| 5 | `/assets/css/style.css` | Định nghĩa CSS tùy chỉnh: biến màu thương hiệu (`--brand-primary: #1a3c6e`, `--brand-secondary: #e8a020`), class `.card-phong` (hover effect), `.btn-brand`, `.btn-gold`, `.gia-thue` (font to, màu xanh navy). | CSS3, Custom Properties |

---

### 🗂️ NHIỆM VỤ 2: Trang Public (Thư mục gốc)

| # | File đường dẫn | Công việc cần code | Kỹ thuật sử dụng |
|---|---------------|-------------------|------------------|
| 6 | `/index.php` | Khai báo `$pageTitle`, `$currentPage = 'index'`. Viết SQL: `SELECT p.*, t.soTang, t.heSoGia, c.tenCaoOc, ROUND(p.donGiaM2 * p.dienTich * t.heSoGia, 0) AS giaThue FROM PHONG p JOIN TANG t ON p.maTang=t.maTang JOIN CAO_OC c ON t.maCaoOc=c.maCaoOc WHERE p.trangThai='Trống' ORDER BY giaThue ASC LIMIT 12`. Render kết quả dạng Bootstrap Card Grid (`row-cols-md-3`). Xử lý filter GET (`?timkiem=`, `?dienTich_min=`). | PHP, MySQLi, Bootstrap Cards, GET filter |
| 7 | `/phong_trong.php` | Trang danh sách toàn bộ phòng trống (không giới hạn 12). Thêm filter nâng cao: lọc theo Cao ốc (dropdown load từ DB), theo tầng, theo giá min-max. Phân trang đơn giản (`LIMIT ... OFFSET ...`). Dùng `include_once` cho header/navbar/footer. | PHP, MySQLi JOIN, Bootstrap Pagination |
| 8 | `/chi_tiet_phong.php` | Nhận `?id=maPhong` từ URL. `real_escape_string()` chống SQLi. Query 1 phòng JOIN TANG, CAO_OC. Hiển thị đầy đủ thông tin, giá thuê tính động. Nút "Đăng ký thuê phòng này" link sang `dang_ky_thue.php?maPhong=...`. | PHP, MySQLi, `real_escape_string()` |
| 9 | `/gioi_thieu.php` | Trang tĩnh giới thiệu công ty. Dùng `include_once` header/navbar/footer. Nội dung: slogan, sứ mệnh, tính năng hệ thống, thông tin liên hệ. | HTML, Bootstrap |
| 10 | `/lien_he.php` | Form liên hệ (Họ tên, SĐT, Nội dung). Submit POST sang `lien_he_submit.php`. Hiển thị bản đồ Google Maps embed. | HTML, Bootstrap Form |

---

### 🗂️ NHIỆM VỤ 3: Module Phòng (trong thư mục `/phong/`)

| # | File đường dẫn | Công việc cần code | Kỹ thuật sử dụng |
|---|---------------|-------------------|------------------|
| 11 | `/phong/phong_hienthi.php` | Gọi `kiemTraSession()`. Query `SELECT p.*, t.soTang, c.tenCaoOc, ROUND(...) AS giaThue FROM PHONG p JOIN TANG ... JOIN CAO_OC ...`. Render bảng Bootstrap (`table-striped table-hover`). Ô tìm kiếm GET (`?tukhoa=`, `?trangThai=`). Mỗi hàng có nút Sửa (link `phong_sua.php?id=`) và Xóa (link `phong_xoa.php?id=`). Hiện thông báo `?thanhcong=` bằng `alert-success`. | PHP, MySQLi JOIN, Bootstrap Table, GET search |
| 12 | `/phong/phong_them.php` | Gọi `kiemTraSession()`. Load dropdown Tầng từ DB: `SELECT t.maTang, t.soTang, t.heSoGia, c.tenCaoOc FROM TANG t JOIN CAO_OC c ...`. Mỗi `<option>` gắn `data-heso="..."`. Ô "Giá thuê" READONLY, tính real-time bằng JS: `Math.round(donGia * dienTich * heso)`. Submit POST → `phong_them_submit.php`. | PHP, JS (oninput), Bootstrap Form |
| 13 | `/phong/phong_them_submit.php` | Validate: `maPhong` không rỗng, chỉ `[A-Za-z0-9\-_]`. `dienTich` > 0. `donGiaM2` > 0. Kiểm tra trùng `maPhong`: `SELECT maPhong FROM PHONG WHERE maPhong='...'`. Query tầng để lấy `heSoGia` rồi tính `giaThue` server-side (không tin JS). INSERT: `INSERT INTO PHONG (maPhong, dienTich, soChoLamViec, moTaViTri, donGiaM2, giaThue, trangThai, maTang) VALUES (...)`. Redirect về `phong_hienthi.php?thanhcong=...` hoặc `phong_them.php?loi=...`. | PHP, MySQLi, `real_escape_string()`, `header()` |
| 14 | `/phong/phong_sua.php` | Nhận `?id=maPhong`. Query 1 phòng. Pre-fill form bằng `value="<?php echo e($row['...']); ?>"`. Dropdown tầng dùng `selected` so sánh `$row['maTang']`. Ô "Giá thuê" READONLY, tính real-time bằng JS. Submit POST → `phong_sua_submit.php`. | PHP, HTML pre-fill, JS |
| 15 | `/phong/phong_sua_submit.php` | Nhận `maPhong` (hidden field). Validate tương tự `_them`. Tính lại `giaThue` server-side. UPDATE: `UPDATE PHONG SET dienTich=..., donGiaM2=..., giaThue=..., maTang=..., moTaViTri=..., trangThai=... WHERE maPhong='...'`. Redirect. | PHP, MySQLi UPDATE, `header()` |
| 16 | `/phong/phong_xoa.php` | Nhận `?id=maPhong`. Kiểm tra ràng buộc: `SELECT COUNT(*) FROM CHI_TIET_HOP_DONG WHERE maPhong='...' AND trangThai='DangThue'`. Nếu còn hợp đồng hoạt động → redirect lỗi, không cho xóa. Nếu an toàn → `DELETE FROM PHONG WHERE maPhong='...'`. Redirect. | PHP, MySQLi, ràng buộc nghiệp vụ |

---

### 🗂️ NHIỆM VỤ 4: Module Cao ốc & Tầng

| # | File đường dẫn | Công việc cần code | Kỹ thuật sử dụng |
|---|---------------|-------------------|------------------|
| 17 | `/cao_oc/cao_oc_hienthi.php` | `kiemTraSession()`. Query `SELECT * FROM CAO_OC ORDER BY tenCaoOc`. Render bảng. Nút Thêm, Sửa, Xóa. Hiện tổng số phòng mỗi cao ốc bằng subquery: `(SELECT COUNT(*) FROM TANG t JOIN PHONG p ON t.maTang=p.maTang WHERE t.maCaoOc=c.maCaoOc) AS tongPhong`. | PHP, MySQLi, Subquery |
| 18 | `/cao_oc/cao_oc_them.php` | Form: maCaoOc, tenCaoOc, diaChi, moTa, tongDienTich. Validate: maCaoOc chỉ `[A-Z0-9\-]`. Submit → `cao_oc_them_submit.php`. | PHP, Bootstrap Form |
| 19 | `/cao_oc/cao_oc_them_submit.php` | Validate + Kiểm tra trùng `maCaoOc` + `INSERT INTO CAO_OC` + redirect. | PHP, MySQLi INSERT |
| 20 | `/cao_oc/cao_oc_sua.php` | Pre-fill form từ DB. Submit → `cao_oc_sua_submit.php`. | PHP |
| 21 | `/cao_oc/cao_oc_sua_submit.php` | Validate + `UPDATE CAO_OC SET ... WHERE maCaoOc='...'` + redirect. | PHP, MySQLi UPDATE |

---

### 🗂️ NHIỆM VỤ 5: Module Báo cáo

| # | File đường dẫn | Công việc cần code | Kỹ thuật sử dụng |
|---|---------------|-------------------|------------------|
| 22 | `/bao_cao/bao_cao.php` | `kiemTraSession()`. Render 4 tab Bootstrap (Tab Phòng trống / Phòng đang thuê / HĐ hết hạn trong tháng / Nhân viên). Mỗi tab gọi 1 truy vấn riêng: **Tab 1** `WHERE p.trangThai='Trống'`; **Tab 2** `WHERE p.trangThai='DangThue'`; **Tab 3** `WHERE hd.trangThai IN ('DangHieuLuc','GiaHan') AND MONTH(hd.ngayHetHanCuoiCung)=MONTH(NOW()) AND YEAR(...)=YEAR(NOW())`; **Tab 4** `WHERE nv.dangLamViec=1`. In kết quả ra `<table>`. Nút "In trang" (`window.print()`). | PHP, MySQLi, Bootstrap Tabs, `window.print()` |

---

## 👤 THÀNH VIÊN 2: TRẦN TRÍ NHÂN (DTH235712) – Back-end Logic & CSDL
**Phụ trách:** Thiết kế CSDL, Session/Phân quyền, Module Hợp đồng (toàn bộ), Module Thanh toán/Bù trừ công nợ, Module Yêu cầu thuê

---

### 🗂️ NHIỆM VỤ 6: Thiết kế Cơ sở dữ liệu

| # | File đường dẫn | Công việc cần code | Kỹ thuật sử dụng |
|---|---------------|-------------------|------------------|
| 23 | `/database/quan_ly_cao_oc.sql` | Viết đầy đủ script SQL: `CREATE DATABASE` + `USE`. Tạo **11 bảng** theo thứ tự (bảng cha trước, bảng con sau để tránh lỗi FK): CAO_OC → TANG → PHONG → NHAN_VIEN → KHACH_HANG → HOP_DONG → CHI_TIET_HOP_DONG → GIA_HAN_HOP_DONG → CHI_TIET_GIA_HAN → HOA_DON → CHI_SO_DIEN_NUOC. Thêm bảng `YEU_CAU_THUE`. Viết `INSERT` dữ liệu mẫu (tối thiểu: 2 cao ốc, 4 tầng, 8 phòng, 3 nhân viên, 2 khách hàng, 1 hợp đồng mẫu có hóa đơn). Thêm `INDEX` trên `trangThai` và `ngayHetHanCuoiCung`. | MySQL DDL, FK Constraint, INDEX, INSERT mẫu |

**Chi tiết bảng cần tạo:**

```sql
-- Bảng mới cần thêm so với thiết kế OOAD:
CREATE TABLE YEU_CAU_THUE (
    maYeuCau    INT AUTO_INCREMENT PRIMARY KEY,
    hoTen       NVARCHAR(100) NOT NULL,
    soDienThoai VARCHAR(15)   NOT NULL,
    email       VARCHAR(100),
    diaChi      VARCHAR(200),
    maPhong     CHAR(10),
    ngayBatDau  DATE,
    thoiGianThue INT DEFAULT 6,
    ghiChu      TEXT,
    trangThai   VARCHAR(20) DEFAULT 'ChoDuyet',  -- ChoDuyet / DaDuyet / TuChoi
    ngayGui     DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_trangThai (trangThai)
);
```

---

### 🗂️ NHIỆM VỤ 7: Đăng nhập & Phân quyền Session

| # | File đường dẫn | Công việc cần code | Kỹ thuật sử dụng |
|---|---------------|-------------------|------------------|
| 24 | `/dangnhap.php` | Form HTML: input `tenDangNhap`, `matKhau` (type=password). Action POST → `dangnhap_submit.php`. Hiển thị lỗi từ `?loi=` bằng `alert-danger`. Thiết kế đẹp: card căn giữa màn hình, logo CAOCENTER. | HTML, Bootstrap Card |
| 25 | `/dangnhap_submit.php` | `session_start()`. Validate: không rỗng. Query: `SELECT * FROM NHAN_VIEN WHERE tenDangNhap='...' AND dangLamViec=1`. Kiểm tra mật khẩu: `password_verify($_POST['matKhau'], $row['matKhau'])`. Nếu đúng: ghi `$_SESSION['maNV']`, `$_SESSION['tenNV']`, `$_SESSION['quyenHan']`, `$_SESSION['boPhan']`. Redirect về trang phù hợp theo quyền. Nếu sai: redirect về `dangnhap.php?loi=...`. | PHP Session, `password_verify()`, `header()` |
| 26 | `/dangxuat.php` | `session_start()` → `session_unset()` → `session_destroy()` → `header("Location: dangnhap.php")` → `exit()`. Không có HTML. | PHP Session |
| 27 | `/thuvien.php` (bổ sung) | Hoàn thiện hàm `kiemTraSession()` và `kiemTraQuyen($quyen)` đã có. Bổ sung hàm `layNoKyTruoc($conn, $soHopDong)` truy vấn HOA_DON lấy `soTienConNo` gần nhất. Hàm `tinhTienPhaiNop($tongGiaThang, $soNoKyTruoc, $soKy)` xử lý bù trừ nợ. | PHP |

---

### 🗂️ NHIỆM VỤ 8: Module Hợp đồng (thư mục `/hop_dong/`)

| # | File đường dẫn | Công việc cần code | Kỹ thuật sử dụng |
|---|---------------|-------------------|------------------|
| 28 | `/hop_dong/hd_hienthi.php` | `kiemTraSession()`. Query: `SELECT hd.*, kh.tenKH, kh.soDienThoai FROM HOP_DONG hd JOIN KHACH_HANG kh ON hd.maKH=kh.maKH ORDER BY hd.ngayHieuLuc DESC`. Filter GET theo `trangThai`, tìm kiếm theo `soHopDong`/`tenKH`. Render bảng, mỗi hàng có: badge trạng thái (dùng `badgeTrangThaiHD()`), link Xem chi tiết / Gia hạn / Hủy. | PHP, MySQLi JOIN, Bootstrap Badge |
| 29 | `/hop_dong/hd_them.php` | **Wizard 4 bước HTML** (dùng JS để ẩn/hiện từng bước, không cần AJAX). **Bước 1:** Ô tìm kiếm KH, load dropdown `KHACH_HANG` từ DB. **Bước 2:** Bảng checkbox các phòng trống (query JOIN). **Bước 3:** Input `ngayHieuLuc`, `thoiGianThue` (min=6). Hiển thị `ngayHetHan` tự tính bằng JS. **Bước 4:** Bảng tóm tắt + tổng tiền kỳ 1 (6 tháng) READONLY. Nút Submit → `hd_them_submit.php`. | PHP, JavaScript Wizard |
| 30 | `/hop_dong/hd_them_submit.php` | **QUAN TRỌNG NHẤT.** `session_start()`. Nhận POST: `maKH`, `maTang[]` (array checkbox phòng), `ngayHieuLuc`, `thoiGianThue`. **Validate:** `thoiGianThue >= 6`. Kiểm tra từng phòng: `WHERE maPhong='...' AND trangThai='Trống'` (nếu có phòng không trống → lỗi). **Transaction:** `$conn->begin_transaction()`. Tạo `soHopDong` duy nhất: `'HD-' . date('Ymd') . '-' . rand(1000,9999)`. `INSERT INTO HOP_DONG`. Vòng lặp `foreach` mảng phòng: `INSERT INTO CHI_TIET_HOP_DONG`. `UPDATE PHONG SET trangThai='DangThue'`. Tính `ngayHetHanCuoiCung = date('Y-m-d', strtotime("+$thoiGianThue months", strtotime($ngayHieuLuc)))`. `UPDATE HOP_DONG SET ngayHetHanCuoiCung=...`. `$conn->commit()`. Nếu lỗi: `$conn->rollback()`. | PHP, MySQLi Transaction, `begin_transaction()`, `commit()`, `rollback()` |
| 31 | `/hop_dong/hd_gia_han.php` | Nhận `?id=soHopDong`. Query hợp đồng + chi tiết phòng đang thuê. **Kiểm tra 3 điều kiện:** (1) HĐ đang hiệu lực, (2) `ngayHetHanCuoiCung <= NOW() + 30 ngày` (dùng `DATE_ADD(NOW(), INTERVAL 30 DAY)`), (3) Không có `HOA_DON` nào `soTienConNo > 0`. Render bảng từng phòng với input số tháng gia hạn. Cột "Ngày hết hạn mới" tính bằng JS real-time. Submit → `hd_gia_han_submit.php`. | PHP, JavaScript real-time, Bootstrap |
| 32 | `/hop_dong/hd_gia_han_submit.php` | Nhận `soHopDong`, `soThangGiaHan[]` (array, mỗi phòng 1 giá trị). **Transaction:** INSERT `GIA_HAN_HOP_DONG`. Vòng lặp: INSERT `CHI_TIET_GIA_HAN` từng phòng. UPDATE `CHI_TIET_HOP_DONG.ngayHetHan` cho từng phòng. Tính `MAX(ngayHetHan)` từ `CHI_TIET_HOP_DONG WHERE soHopDong=... AND trangThai='DangThue'` rồi `UPDATE HOP_DONG SET ngayHetHanCuoiCung=..., trangThai='GiaHan'`. Commit/Rollback. | PHP, MySQLi Transaction, `MAX()` |
| 33 | `/hop_dong/hd_ket_thuc_le.php` | Nhận `?id=soHopDong`. Kiểm tra HĐ có ≥ 2 phòng đang thuê. Hiển thị danh sách phòng có checkbox. Nút Submit → `hd_ket_thuc_le_submit.php`. | PHP, HTML Checkbox |
| 34 | `/hop_dong/hd_ket_thuc_le_submit.php` | Nhận `soHopDong`, `maPhong[]`. Kiểm tra: số phòng trả < tổng phòng đang thuê (nếu bằng → lỗi, redirect sang hủy hợp đồng). Kiểm tra công nợ từng phòng. **Transaction:** `UPDATE CHI_TIET_HOP_DONG SET trangThai='DaKetThuc'`. `UPDATE PHONG SET trangThai='Trống'`. Query `MAX(ngayHetHan) WHERE trangThai='DangThue'` → `UPDATE HOP_DONG SET ngayHetHanCuoiCung=...`. Commit/Rollback. | PHP, MySQLi Transaction |
| 35 | `/hop_dong/hd_huy.php` | Nhận `?id=soHopDong`. Kiểm tra: không có hóa đơn nào `soTienConNo > 0` (nếu còn nợ → hiển thị cảnh báo đỏ, disable nút xác nhận). Form gồm: `ngayHuy` (date), `lyDoHuy` (textarea). Submit → `hd_huy_submit.php`. | PHP, Bootstrap Danger Alert |
| 36 | `/hop_dong/hd_huy_submit.php` | Kiểm tra lại công nợ lần cuối (server-side). **Transaction:** `UPDATE HOP_DONG SET trangThai='DaHuy', ngayHuy=..., lyDoHuy=...`. `UPDATE CHI_TIET_HOP_DONG SET trangThai='DaKetThuc' WHERE soHopDong=...`. `UPDATE PHONG SET trangThai='Trống' WHERE maPhong IN (SELECT maPhong FROM CHI_TIET_HOP_DONG WHERE soHopDong=...)`. Commit/Rollback. | PHP, MySQLi Transaction, Subquery |

---

### 🗂️ NHIỆM VỤ 9: Module Thanh toán (thư mục `/thanh_toan/`)

| # | File đường dẫn | Công việc cần code | Kỹ thuật sử dụng |
|---|---------------|-------------------|------------------|
| 37 | `/thanh_toan/tt_tao.php` | `kiemTraSession()`. Form tìm hợp đồng: input `soHopDong` + nút Tìm (submit GET). Sau khi tìm: hiển thị thông tin KH + bảng chi tiết phòng và giá thuê. **Hộp bù trừ công nợ** (màu vàng): gọi `layNoKyTruoc($conn, $soHopDong)`, hiển thị với giải thích "âm = dư, dương = nợ". Tính `soTienPhaiNop = tongTien + soNoKyTruoc`. Input `soTienDaNop`. JS real-time: cập nhật `soTienConNo = soTienPhaiNop - soTienDaNop`, đổi màu nếu dương/âm. Submit POST → `tt_tao_submit.php`. | PHP, MySQLi, JavaScript `oninput` |
| 38 | `/thanh_toan/tt_tao_submit.php` | **Logic nghiệp vụ cốt lõi.** Nhận `soHopDong`, `soTienDaNop`. Validate: không rỗng, là số, ≥ 0. Tính server-side: `$noKyTruoc = layNoKyTruoc($conn, $soHopDong)`. Query tổng tiền phòng đang thuê. `$soKy = layKyTiepTheo($conn, $soHopDong)`. `$tongTien = ($soKy == 1) ? $tongGiaThang * 6 : $tongGiaThang`. `$soTienPhaiNop = $tongTien + $noKyTruoc`. `$soTienConNo = $soTienPhaiNop - $soTienDaNop`. `$trangThai = ($soTienConNo <= 0) ? 'DaThu' : 'ConNo'`. INSERT `HOA_DON`: `soPhieu = 'HD' . date('YmdHis')`, `tongTien`, `soTienDaNop`, `soTienConNo`, `kyThanhToan=$soKy`, `maNV=$_SESSION['maNV']`. Redirect. | PHP, MySQLi INSERT, Nghiệp vụ bù trừ nợ |
| 39 | `/thanh_toan/dien_nuoc_ghi.php` | `kiemTraSession()`. Form: chọn phòng (dropdown load từ DB các phòng `DangThue`), chọn `thangGhi`/`namGhi`, input chỉ số đầu kỳ/cuối kỳ điện + nước, đơn giá. JS tính real-time tổng chi phí. Kiểm tra `UNIQUE(maPhong, thangGhi, namGhi)` trước khi cho nhập. Submit → `dien_nuoc_ghi_submit.php`. | PHP, JavaScript, Bootstrap Form |
| 40 | `/thanh_toan/dien_nuoc_ghi_submit.php` | Validate: chỉ số cuối ≥ chỉ số đầu. Kiểm tra không trùng: `SELECT COUNT(*) FROM CHI_SO_DIEN_NUOC WHERE maPhong=... AND thangGhi=... AND namGhi=...` → lỗi nếu đã có. INSERT `CHI_SO_DIEN_NUOC`. Sau đó INSERT `HOA_DON` (lyDo='TienDien' và 'TienNuoc' riêng hoặc gộp). Redirect. | PHP, MySQLi, UNIQUE constraint check |

---

### 🗂️ NHIỆM VỤ 10: Module Yêu cầu thuê (Admin xử lý)

| # | File đường dẫn | Công việc cần code | Kỹ thuật sử dụng |
|---|---------------|-------------------|------------------|
| 41 | `/dang_ky_thue_submit.php` | (Xem chi tiết ở Nhiệm vụ 3 – do **Nhân** viết phần submit). Nhận POST từ `dang_ky_thue.php`. Validate: `hoTen` không rỗng, `soDienThoai` format `^[0-9]{9,11}$`, `maPhong` không rỗng, `thoiGianThue >= 6`. `real_escape_string()` tất cả trường text. INSERT `YEU_CAU_THUE`. Redirect về `dang_ky_thue.php?thanhcong=Gửi+yêu+cầu+thành+công...` hoặc `?loi=...`. | PHP, MySQLi, `real_escape_string()`, `header()` |
| 42 | `/yeu_cau_thue/yct_hienthi.php` | `kiemTraSession()`, `kiemTraQuyen([1, 2])`. Query `SELECT * FROM YEU_CAU_THUE ORDER BY ngayGui DESC`. Render bảng có cột: Họ tên / SĐT / Phòng muốn thuê / Ngày gửi / Trạng thái (badge) / Hành động (Duyệt / Từ chối). | PHP, MySQLi, Bootstrap Badge |
| 43 | `/yeu_cau_thue/yct_duyet.php` | Nhận `?id=maYeuCau`. `UPDATE YEU_CAU_THUE SET trangThai='DaDuyet' WHERE maYeuCau=...`. Redirect về `yct_hienthi.php?thanhcong=...`. | PHP, MySQLi UPDATE |

---

## 📊 TỔNG KẾT PHÂN CÔNG

| | Nhật (DTH235713) | Nhân (DTH235712) |
|--|--|--|
| **Số file phụ trách** | ~22 file | ~20 file |
| **Trọng tâm** | Giao diện, UX, Bootstrap | Logic nghiệp vụ, Transaction, SQL phức tạp |
| **Kỹ thuật nổi bật** | Bootstrap Grid/Card/Tab, JS real-time preview, CSS Custom Properties | MySQLi Transaction, Session phân quyền, Bù trừ nợ tự động |
| **Tỉ lệ** | **50%** | **50%** |

---

## 🗓️ ĐỀ XUẤT LỊCH LÀM VIỆC

| Tuần | Nhật làm | Nhân làm |
|------|----------|----------|
| **Tuần 1** | Thiết kế `style.css`, `header.php`, `navbar.php`, `footer.php`, `banner.php` | Viết toàn bộ `quan_ly_cao_oc.sql` (11 bảng + dữ liệu mẫu), `dangnhap.php`, `dangnhap_submit.php`, `dangxuat.php` |
| **Tuần 2** | Hoàn thiện `index.php`, `phong_trong.php`, `chi_tiet_phong.php`, `gioi_thieu.php` | Viết `hd_them.php` + `hd_them_submit.php` (Transaction), `hd_huy.php` + `hd_huy_submit.php` |
| **Tuần 3** | Hoàn thiện Module Phòng (`phong_hienthi/them/sua/xoa`), Module Cao ốc | Viết `hd_gia_han_submit.php`, `hd_ket_thuc_le_submit.php`, `tt_tao_submit.php` (bù trừ nợ), `dien_nuoc_ghi_submit.php` |
| **Tuần 4** | Hoàn thiện `bao_cao.php`, `dang_ky_thue.php`, `lien_he.php` | Hoàn thiện `yct_hienthi.php`, fix bug, kiểm tra toàn bộ luồng nghiệp vụ |
| **Tuần 5** | Test giao diện responsive, fix CSS, chuẩn bị slide báo cáo | Test toàn bộ chức năng, chuẩn bị giải thích code cho GV hỏi |

---

## ⚠️ QUY TẮC CODE CHUNG CẢ NHÓM

1. **Không hardcode:** Mọi danh sách (cao ốc, tầng, trạng thái từ DB) phải `fetch_assoc()` từ DB
2. **Cặp file:** Mỗi chức năng = 1 file form (`_them.php`) + 1 file xử lý (`_them_submit.php`)
3. **Chống SQLi:** Dùng `$conn->real_escape_string()` cho mọi biến từ người dùng trước khi đưa vào SQL
4. **Chống XSS:** Dùng hàm `e()` (wrapper của `htmlspecialchars()`) khi in dữ liệu ra HTML
5. **File _submit.php:** Chỉ có PHP, không có HTML. Luôn kết thúc bằng `header("Location:...")` + `exit()`
6. **Session:** Mọi trang admin phải gọi `kiemTraSession()` ở dòng đầu tiên
