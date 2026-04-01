    <footer class="footer-new">
        <div class="container-wide">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="d-flex align-items-center mb-4">
                        <img src="<?php echo $basePath; ?>assets/images/logo-k.svg" height="32" alt="Logo" class="me-2 brightness-0 invert">
                        <span class="fw-800 fs-4 tracking-tight text-white">NHK MOBILE</span>
                    </div>
                    <p>Đại lý ủy quyền chính thức của Apple tại Việt Nam. Chúng tôi cam kết mang đến những siêu phẩm công nghệ với dịch vụ hậu mãi chuẩn 5 sao.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="nav-icon bg-dark border border-secondary"><i class="bi bi-facebook text-white"></i></a>
                        <a href="#" class="nav-icon bg-dark border border-secondary"><i class="bi bi-instagram text-white"></i></a>
                        <a href="#" class="nav-icon bg-dark border border-secondary"><i class="bi bi-youtube text-white"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-title">Khám phá</h4>
                    <ul class="footer-links p-0">
                        <li><a href="product.php?category=Apple">iPhone</a></li>
                        <li><a href="product.php?category=Samsung">Samsung</a></li>
                        <li><a href="product.php">Tất cả điện thoại</a></li>
                        <li><a href="news.php">Tin tức công nghệ</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-title">Dịch vụ</h4>
                    <ul class="footer-links p-0">
                        <li><a href="warranty.php">Chính sách bảo hành</a></li>
                        <li><a href="#">Vận chuyển & Giao hàng</a></li>
                        <li><a href="#">Hình thức thanh toán</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-title">Kết nối</h4>
                    <ul class="footer-links p-0">
                        <li class="text-muted small mb-3"><i class="bi bi-geo-alt me-2 text-primary"></i> 123 Đường Công Nghệ, Quận 1, TP.HCM</li>
                        <li class="text-muted small mb-3"><i class="bi bi-telephone me-2 text-primary"></i> 1800 1234 (Miễn phí)</li>
                        <li class="text-muted small mb-3"><i class="bi bi-envelope me-2 text-primary"></i> contact@nhkmobile.vn</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom-new">
                <p class="mb-0">&copy; 2026 NHK Mobile. Thiết kế bởi NHK Team.</p>
                <div class="d-flex gap-4">
                    <a href="#" class="text-decoration-none">Quyền riêng tư</a>
                    <a href="#" class="text-decoration-none">Điều khoản</a>
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
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.navbar-minimal');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
