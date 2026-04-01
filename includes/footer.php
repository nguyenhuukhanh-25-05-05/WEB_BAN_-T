    <footer class="footer-new bg-light mt-5 border-top">
        <div class="container-wide py-5">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="d-flex align-items-center mb-4">
                        <div class="logo-box me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; border: 2px solid var(--text-main); border-radius: 10px; background: #fff;">
                            <span class="fw-900 fs-5 text-main">NHK</span>
                        </div>
                        <span class="fw-800 fs-5 tracking-tight text-main">MOBILE</span>
                    </div>
                    <p class="text-secondary small">Đại lý ủy quyền chính thức của Apple tại Việt Nam. Chúng tôi cam kết mang đến những siêu phẩm công nghệ với dịch vụ hậu mãi chuẩn 5 sao.</p>
                    <div class="d-flex gap-2 mt-4">
                        <a href="#" class="nav-icon bg-white border border-light shadow-sm" style="width: 38px; height: 38px;"><i class="bi bi-facebook text-primary small"></i></a>
                        <a href="#" class="nav-icon bg-white border border-light shadow-sm" style="width: 38px; height: 38px;"><i class="bi bi-instagram text-danger small"></i></a>
                        <a href="#" class="nav-icon bg-white border border-light shadow-sm" style="width: 38px; height: 38px;"><i class="bi bi-youtube text-danger small"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h6 class="footer-title fw-800 small text-uppercase mb-4">Khám phá</h6>
                    <ul class="footer-links p-0 list-unstyled small">
                        <li class="mb-2"><a href="product.php?category=Apple" class="text-secondary text-decoration-none">iPhone</a></li>
                        <li class="mb-2"><a href="product.php?category=Samsung" class="text-secondary text-decoration-none">Samsung</a></li>
                        <li class="mb-2"><a href="product.php" class="text-secondary text-decoration-none">Tất cả điện thoại</a></li>
                        <li class="mb-2"><a href="news.php" class="text-secondary text-decoration-none">Tin tức công nghệ</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h6 class="footer-title fw-800 small text-uppercase mb-4">Dịch vụ</h6>
                    <ul class="footer-links p-0 list-unstyled small">
                        <li class="mb-2"><a href="warranty.php" class="text-secondary text-decoration-none">Chính sách bảo hành</a></li>
                        <li class="mb-2"><a href="#" class="text-secondary text-decoration-none">Vận chuyển & Giao hàng</a></li>
                        <li class="mb-2"><a href="#" class="text-secondary text-decoration-none">Hình thức thanh toán</a></li>
                        <li class="mb-2"><a href="#" class="text-secondary text-decoration-none">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h6 class="footer-title fw-800 small text-uppercase mb-4">Kết nối</h6>
                    <ul class="footer-links p-0 list-unstyled small">
                        <li class="text-muted mb-3"><i class="bi bi-geo-alt me-2 text-primary"></i> 123 Đường Công Nghệ, Quận 1, TP.HCM</li>
                        <li class="text-muted mb-3"><i class="bi bi-telephone me-2 text-primary"></i> 1800 1234 (Miễn phí)</li>
                        <li class="text-muted mb-3"><i class="bi bi-envelope me-2 text-primary"></i> contact@nhkmobile.vn</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom-new pt-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <p class="mb-0 text-muted small">&copy; 2026 NHK Mobile. Designed by NHK Team.</p>
                <div class="d-flex gap-4">
                    <a href="#" class="text-decoration-none text-muted small">Quyền riêng tư</a>
                    <a href="#" class="text-decoration-none text-muted small">Điều khoản</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Search Overlay -->
    <?php include 'includes/search_overlay.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $basePath; ?>assets/js/search-overlay.js"></script>
    <script>
        // Navbar scroll effect
        (function() {
            const nav = document.querySelector('.navbar-minimal');
            if (nav) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 50) {
                        nav.classList.add('scrolled');
                    } else {
                        nav.classList.remove('scrolled');
                    }
                });
            }
        })();
    </script>
</body>
</html>
