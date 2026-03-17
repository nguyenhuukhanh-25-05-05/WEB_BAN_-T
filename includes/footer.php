    <footer class="footer">
        <div class="container footer-trust-badges">
            <div class="trust-badge">
                <i class="bi bi-truck"></i>
                <div class="trust-badge-text">
                    <span class="trust-badge-title">Giao hàng miễn phí</span>
                    <span class="trust-badge-desc">Cho đơn hàng từ 5tr</span>
                </div>
            </div>
            <div class="trust-badge">
                <i class="bi bi-shield-check"></i>
                <div class="trust-badge-text">
                    <span class="trust-badge-title">Bảo hành 12 tháng</span>
                    <span class="trust-badge-desc">Chính hãng 100%</span>
                </div>
            </div>
            <div class="trust-badge">
                <i class="bi bi-credit-card"></i>
                <div class="trust-badge-text">
                    <span class="trust-badge-title">Trả góp 0%</span>
                    <span class="trust-badge-desc">Thủ tục đơn giản</span>
                </div>
            </div>
            <div class="trust-badge">
                <i class="bi bi-arrow-repeat"></i>
                <div class="trust-badge-text">
                    <span class="trust-badge-title">Đổi trả 30 ngày</span>
                    <span class="trust-badge-desc">Nếu có lỗi phần cứng</span>
                </div>
            </div>
        </div>

        <div class="container footer-links-grid">
            <div class="row text-md-center">
                <div class="col-md-4 footer-col">
                    <h6>DÒNG SẢN PHẨM</h6>
                    <ul>
                        <li><a href="<?php echo $basePath; ?>product.php">iPhone 15 Series</a></li>
                        <li><a href="#">Samsung Galaxy S24</a></li>
                        <li><a href="#">Xiaomi 14 Ultra</a></li>
                        <li><a href="#">Oppo Find X7</a></li>
                    </ul>
                </div>
                <div class="col-md-4 footer-col">
                    <h6>HỖ TRỢ KHÁCH HÀNG</h6>
                    <ul>
                        <li><a href="<?php echo $basePath; ?>warranty.php">Chính sách bảo hành</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Giao hàng & Thanh toán</a></li>
                    </ul>
                </div>
                <div class="col-md-4 footer-col">
                    <h6>THÔNG TIN LIÊN HỆ</h6>
                    <ul>
                        <li><a href="tel:0333427187">Hotline: 0333 427 187</a></li>
                        <li><a href="#">Địa chỉ: 123 Cầu Giấy, Hà Nội</a></li>
                        <li><a href="#">Email: support@nhkmobile.vn</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container footer-bottom">
            <div class="footer-bottom-info">
                <div>© 2026 NHK Mobile. Bảo lưu mọi quyền.</div>
            </div>
            <div class="footer-socials">
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-instagram"></i></a>
                <a href="#"><i class="bi bi-tiktok"></i></a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Search Overlay -->
    <?php include __DIR__ . '/search_overlay.php'; ?>
    <script src="<?php echo isset($basePath) ? $basePath : ''; ?>assets/js/search-overlay.js"></script>
</body>
</html>
