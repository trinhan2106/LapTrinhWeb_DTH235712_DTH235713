# 🏢 Web Hệ thống Quản lý Vận hành Cho thuê Cao ốc

## Thông tin đồ án

| Thông tin | Chi tiết |
|-----------|----------|
| **Tên đề tài** | Web Hệ thống Quản lý Vận hành Cho thuê Cao ốc |
| **Môn học** | Lập trình Web |
| **Sinh viên 1** | Trần Trí Nhân – MSSV: DTH235712 |
| **Sinh viên 2** | Huỳnh Minh Nhật – MSSV: DTH235713 |
| **Lớp** | DH24TH2 |
| **Trường** | Đại học An Giang |
| **Năm học** | 2025 – 2026 |

---

## 📋 Giới thiệu hệ thống

Hệ thống hỗ trợ số hóa toàn bộ nghiệp vụ cho thuê văn phòng tại các cao ốc thương mại. Thay vì quản lý thủ công bằng giấy tờ, phần mềm tập trung xử lý toàn bộ vòng đời của một hợp đồng: từ khi lập hợp đồng, thanh toán hằng tháng có bù trừ nợ/dư tự động, gia hạn linh hoạt từng phòng, cho đến khi chấm dứt hợp đồng.

**Điểm nổi bật:**
- ✅ **Bù trừ nợ/dư tự động** giữa các kỳ thanh toán (nộp thiếu → nợ chuyển kỳ sau; nộp dư → trừ bớt kỳ sau)
- ✅ **Gia hạn linh hoạt từng phòng** riêng biệt trong cùng một hợp đồng
- ✅ **Giá thuê tự động tính** theo công thức: `donGiaM2 × dienTich × heSoGia` – field chỉ đọc, không cho nhập tay
- ✅ **Phân quyền 3 cấp** qua Session: Admin / Quản lý Nhà / Kế toán
- ✅ **Transaction** cho các nghiệp vụ quan trọng: hủy hợp đồng, kết thúc thuê phòng lẻ
- ✅ Giao diện Bootstrap 5 chuẩn 3-panel (Header – Menu trái – Content)

---

## 🛠️ Công nghệ sử dụng

| Thành phần | Công nghệ |
|------------|-----------|
| **Frontend** | HTML5, CSS3, Bootstrap 5, jQuery |
| **Backend** | PHP thuần (Native PHP, **không** dùng Laravel/CodeIgniter) |
| **Cơ sở dữ liệu** | MySQL – kết nối qua MySQLi hướng đối tượng |
| **Môi trường** | XAMPP / Vertrigo / WAMP |

---

## 📁 Cấu trúc thư mục Source Code

```
quan_ly_cao_oc/
│
├── 📄 cauhinh.php                  ← Kết nối CSDL (MySQLi OOP)
├── 📄 thuvien.php                  ← Hàm dùng chung: dinhDangNgay(), dinhDangTien(),
│                                      kiemTraSession(), tinhGiaThue(), badgeTrangThai()...
├── 📄 dangnhap.php                 ← Form đăng nhập hệ thống
├── 📄 dangnhap_submit.php          ← Xử lý đăng nhập: query DB + ghi $_SESSION + redirect
├── 📄 dangxuat.php                 ← Hủy session_destroy() + redirect về dangnhap.php
│
├── 📁 phong/                       ═══ MODULE: QUẢN LÝ PHÒNG (Nhật) ═══
│   ├── phong_hienthi.php           ← Danh sách phòng: query JOIN + lọc trangThai + tìm kiếm
│   ├── phong_them.php              ← Form HTML thêm phòng, tính giá thuê preview bằng JS
│   ├── phong_them_submit.php       ← Validate + INSERT PHONG + header("Location:...")
│   ├── phong_sua.php               ← Pre-fill form từ DB theo ?id=
│   ├── phong_sua_submit.php        ← Validate + UPDATE PHONG + header("Location:...")
│   └── phong_xoa.php               ← Kiểm tra ràng buộc + DELETE + header("Location:...")
│
├── 📁 cao_oc/                      ═══ MODULE: CAO ỐC & TẦNG (Nhật) ═══
│   ├── cao_oc_hienthi.php
│   ├── cao_oc_them.php
│   ├── cao_oc_them_submit.php
│   ├── cao_oc_sua.php
│   └── cao_oc_sua_submit.php
│
├── 📁 khach_hang/                  ═══ MODULE: KHÁCH HÀNG (Nhật) ═══
│   ├── kh_hienthi.php
│   ├── kh_them.php
│   ├── kh_them_submit.php
│   ├── kh_sua.php
│   ├── kh_sua_submit.php
│   └── kh_xoa.php
│
├── 📁 hop_dong/                    ═══ MODULE: HỢP ĐỒNG (Nhân – PHỨC TẠP NHẤT) ═══
│   ├── hd_hienthi.php              ← Danh sách hợp đồng, lọc trạng thái
│   ├── hd_them.php                 ← Form lập HĐ: chọn phòng trống, tính giá tự động
│   ├── hd_them_submit.php          ← Validate ≥6 tháng + INSERT HOP_DONG +
│   │                                  INSERT CHI_TIET_HOP_DONG + UPDATE PHONG
│   ├── hd_gia_han.php              ← Form gia hạn: kiểm tra 3 điều kiện + chọn tháng/phòng
│   ├── hd_gia_han_submit.php       ← INSERT GIA_HAN + UPDATE ngayHetHan từng phòng
│   ├── hd_ket_thuc_le.php          ← Trả bớt phòng, giữ nguyên phòng còn lại
│   ├── hd_ket_thuc_le_submit.php   ← UPDATE CTHD + UPDATE PHONG trong transaction
│   ├── hd_huy.php                  ← Form hủy HĐ: kiểm tra công nợ trước khi cho hủy
│   └── hd_huy_submit.php           ← UPDATE HOP_DONG + CTHD + PHONG trong 1 transaction
│
├── 📁 thanh_toan/                  ═══ MODULE: THANH TOÁN & BÙ TRỪ NỢ (Nhân) ═══
│   ├── tt_tao.php                  ← Tìm HĐ + hiển thị bù trừ nợ kỳ trước real-time
│   ├── tt_tao_submit.php           ← Tính soTienConNo + INSERT HOA_DON + UPDATE trạng thái
│   ├── dien_nuoc_ghi.php           ← Form ghi chỉ số điện/nước
│   └── dien_nuoc_ghi_submit.php    ← INSERT CHI_SO_DIEN_NUOC + tạo HOA_DON dịch vụ
│
├── 📁 bao_cao/                     ═══ MODULE: BÁO CÁO (Nhật) ═══
│   └── bao_cao.php                 ← 4 loại BC: phòng trống, đang thuê, HĐ hết hạn, NV
│
├── 📁 nhan_vien/                   ═══ MODULE: NHÂN VIÊN (Admin) (Nhân) ═══
│   ├── nv_hienthi.php
│   ├── nv_them.php
│   └── nv_them_submit.php
│
├── 📁 assets/
│   ├── css/style.css               ← CSS tùy chỉnh bổ sung Bootstrap
│   └── js/main.js                  ← JS: tính giá thuê real-time, bù trừ nợ real-time
│
└── 📁 database/
    └── quan_ly_cao_oc.sql          ← CREATE TABLE (11 bảng) + INSERT dữ liệu mẫu
```

> **Quy tắc tách file:** Mỗi chức năng gồm 2 file: `[ten]_hienthi/them/sua.php` (giao diện HTML) và `[ten]_submit.php` (xử lý logic POST). File `_submit.php` **không có HTML** — chỉ validate, truy vấn DB, rồi `header("Location: ...")`.

---

## ✅ Danh sách chức năng chính

| STT | Chức năng | Phân quyền |
|-----|-----------|------------|
| 1 | Đăng nhập / Đăng xuất / Phân quyền Session | Tất cả |
| 2 | Quản lý Cao ốc & Tầng (CRUD) | Admin, Quản lý Nhà |
| 3 | Quản lý Phòng – CRUD, lọc trạng thái, tìm kiếm | Admin, Quản lý Nhà |
| 4 | Quản lý Khách hàng – CRUD, lịch sử giao dịch | Admin, Quản lý Nhà |
| 5 | Lập hợp đồng thuê – kiểm tra phòng trống, tính giá tự động, ràng buộc ≥ 6 tháng | Quản lý Nhà |
| 6 | Gia hạn hợp đồng – gia hạn từng phòng riêng, kiểm tra 3 điều kiện | Quản lý Nhà |
| 7 | Kết thúc thuê phòng lẻ – trả bớt phòng, giữ phòng còn lại | Quản lý Nhà |
| 8 | Hủy hợp đồng – kiểm tra nợ trước khi hủy, transaction toàn vẹn | Quản lý Nhà |
| 9 | Lập hóa đơn thanh toán – **bù trừ nợ/dư tự động** từ kỳ trước | Kế toán |
| 10 | Ghi chỉ số điện/nước – tính tiêu thụ, lập hóa đơn dịch vụ | Kế toán |
| 11 | Báo cáo thống kê – 4 loại báo cáo theo yêu cầu nghiệp vụ | Quản lý Nhà |

---

## 🚀 Hướng dẫn cài đặt

### Yêu cầu môi trường
- PHP >= 7.4
- MySQL >= 5.7
- XAMPP hoặc Vertrigo

### Bước 1: Copy source code

Giải nén và copy thư mục `quan_ly_cao_oc` vào thư mục web:
- **XAMPP:** `C:\xampp\htdocs\quan_ly_cao_oc\`
- **Vertrigo:** `C:\VertrigoServ\www\quan_ly_cao_oc\`

### Bước 2: Import cơ sở dữ liệu

1. Truy cập `http://localhost/phpmyadmin`
2. Nhấn **"New"** → Đặt tên database: **`quan_ly_cao_oc`** → **"Create"**
3. Chọn database vừa tạo → Tab **"Import"** → **"Choose File"** → chọn file `database/quan_ly_cao_oc.sql`
4. Nhấn **"Go"** — hệ thống sẽ tạo 11 bảng và import dữ liệu mẫu

### Bước 3: Cấu hình kết nối Database

Mở file `cauhinh.php`, chỉnh sửa thông số cho phù hợp:

```php
$servername = "localhost";      // Giữ nguyên
$username   = "root";           // Giữ nguyên
$password   = "vertrigo";       // XAMPP: đổi thành "" | Vertrigo: giữ "vertrigo"
$dbname     = "quan_ly_cao_oc"; // Phải trùng với tên DB đã tạo ở Bước 2
```

### Bước 4: Chạy hệ thống

Mở trình duyệt, truy cập: **`http://localhost/quan_ly_cao_oc/dangnhap.php`**

**Tài khoản mặc định (mật khẩu MD5):**

| Tên đăng nhập | Mật khẩu | Quyền |
|---------------|----------|-------|
| `admin` | `123456` | Admin – toàn quyền |
| `quanly` | `123456` | Bộ phận Quản lý Nhà |
| `ketoan` | `123456` | Phòng Kế toán |

---

## 📐 Ghi chú kỹ thuật

| Vấn đề | Giải pháp |
|--------|-----------|
| Tránh sai số tiền tệ | Tất cả cột tiền dùng `DECIMAL(15,2)`, không dùng `FLOAT` |
| Giá thuê nhất quán | Tính tự động `giaThue = donGiaM2 × dienTich × heSoGia`, field `readonly` |
| Bù trừ nợ | `soTienConNo = soTienPhaiNop − soTienDaNop` (âm = dư → trừ kỳ sau; dương = nợ → cộng kỳ sau) |
| Bảo mật | Dùng `real_escape_string()` chống SQL Injection; mật khẩu mã hóa MD5 |
| Toàn vẹn dữ liệu | Hủy HĐ và kết thúc phòng lẻ chạy trong `START TRANSACTION ... COMMIT/ROLLBACK` |

---

*Đồ án môn Lập trình Web – Khoa Công nghệ Thông tin – Đại học An Giang – 2026*
