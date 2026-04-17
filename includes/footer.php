<!--
    FOOTER.PHP – Thông tin cuối trang dùng chung
    Gọi bằng: include_once "includes/footer.php";

    Phải được đặt CUỐI CÙNG trong body, trước </body>
-->

<!-- ── FOOTER CHÍNH ───────────────────────────────────────── -->
<footer class="pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">

            <!-- Cột 1: Giới thiệu công ty -->
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span style="font-size:2rem;">🏢</span>
                    <div>
                        <div class="text-white fw-bold fs-5">CAOCENTER</div>
                        <div class="text-warning small">Cho thuê Văn phòng Cao ốc</div>
                    </div>
                </div>
                <p class="small" style="color:#aab4be; line-height:1.7;">
                    Hệ thống quản lý và cho thuê văn phòng chuyên nghiệp. 
                    Không gian làm việc hiện đại, tiện nghi tại các vị trí 
                    đắc địa trong thành phố.
                </p>
                <!-- Icon mạng xã hội -->
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="btn btn-sm btn-outline-secondary text-white border-secondary">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-secondary text-white border-secondary">
                        <i class="bi bi-youtube"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-secondary text-white border-secondary">
                        <i class="bi bi-envelope"></i>
                    </a>
                </div>
            </div>

            <!-- Cột 2: Liên kết nhanh -->
            <div class="col-md-2">
                <h6 class="text-white fw-bold mb-3 text-uppercase" style="letter-spacing:1px;">
                    Liên kết
                </h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="index.php">🏠 Trang chủ</a></li>
                    <li class="mb-2"><a href="phong_trong.php">🚪 Phòng trống</a></li>
                    <li class="mb-2"><a href="gioi_thieu.php">ℹ️ Giới thiệu</a></li>
                    <li class="mb-2"><a href="dang_ky_thue.php">📝 Đăng ký thuê</a></li>
                    <li class="mb-2"><a href="lien_he.php">📞 Liên hệ</a></li>
                </ul>
            </div>

            <!-- Cột 3: Thông tin liên hệ -->
            <div class="col-md-3">
                <h6 class="text-white fw-bold mb-3 text-uppercase" style="letter-spacing:1px;">
                    Liên hệ
                </h6>
                <ul class="list-unstyled small" style="color:#aab4be;">
                    <li class="mb-2">
                        <i class="bi bi-geo-alt-fill text-warning me-2"></i>
                        123 Đường Láng, Đống Đa, Hà Nội
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone-fill text-warning me-2"></i>
                        (028) 1234 5678
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope-fill text-warning me-2"></i>
                        lienhe@caocenter.vn
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-clock-fill text-warning me-2"></i>
                        Thứ 2 – Thứ 6: 8:00 – 17:30
                    </li>
                </ul>
            </div>

            <!-- Cột 4: Giờ làm việc / Bản đồ -->
            <div class="col-md-3">
                <h6 class="text-white fw-bold mb-3 text-uppercase" style="letter-spacing:1px;">
                    Hỗ trợ khách hàng
                </h6>
                <div class="card bg-white bg-opacity-10 border-0 rounded-3 p-3">
                    <div class="small text-white">
                        <div class="mb-2">
                            <i class="bi bi-headset text-warning me-2"></i>
                            <strong>Hotline tư vấn:</strong>
                        </div>
                        <div class="fs-5 fw-bold text-warning mb-2">0909 123 456</div>
                        <div style="color:#aab4be;">
                            Miễn phí · Hoạt động 7 ngày/tuần
                        </div>
                    </div>
                </div>
                <a href="dang_ky_thue.php"
                   class="btn btn-warning btn-sm w-100 mt-3 fw-semibold">
                    <i class="bi bi-pencil-square me-2"></i>Đăng ký tham quan ngay
                </a>
            </div>

        </div><!-- end row -->
    </div><!-- end container -->

    <!-- ── DÒNG BẢN QUYỀN ─────────────────────────────────── -->
    <div class="border-top mt-4 pt-3" style="border-color:#2d4a6e !important;">
        <div class="container">
            <div class="row align-items-center small" style="color:#7a8a9e;">
                <div class="col-md-6 text-center text-md-start">
                    © <?php echo date('Y'); ?> CAOCENTER – Hệ thống Quản lý Cho thuê Cao ốc.
                    Đồ án môn Lập trình Web – ĐH An Giang.
                </div>
                <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                    Thực hiện bởi:
                    <strong class="text-warning">Trần Trí Nhân</strong> &
                    <strong class="text-warning">Huỳnh Minh Nhật</strong>
                    – Lớp DH24TH2
                </div>
            </div>
        </div>
    </div>

</footer>

<!-- ── BOOTSTRAP JS ───────────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>