    <footer class="footer-new bg-light mt-5 border-top">
        <div class="container-wide py-5">
            <div class="footer-grid">
                <!-- Brand Section -->
                <div class="footer-brand">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-4">
                        <div class="logo-box me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; border: 2px solid var(--text-main); border-radius: 10px; background: #fff;">
                            <span class="fw-900 fs-5 text-main">NHK</span>
                        </div>
                        <span class="fw-800 fs-5 tracking-tight text-main">MOBILE</span>
                    </div>
                    <p class="text-secondary small" style="max-width: 300px;">Đại lý ủy quyền chính thức của Apple tại Việt Nam. Dịch vụ hậu mãi chuẩn 5 sao.</p>
                    <div class="d-flex justify-content-center justify-content-md-start gap-2 mt-4 mb-4 mb-md-0">
                        <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-tiktok"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                
                <!-- Explorar Column (Accordion on Mobile) -->
                <div class="footer-col accordion-item-mobile">
                    <div class="footer-title-wrapper d-flex justify-content-between align-items-center">
                        <h6 class="footer-title mb-0">Khám phá</h6>
                        <i class="bi bi-plus-lg d-md-none toggle-icon"></i>
                    </div>
                    <ul class="footer-links p-0 list-unstyled small accordion-content-mobile">
                        <li><a href="product.php?category=Apple">iPhone</a></li>
                        <li><a href="product.php?category=Samsung">Samsung</a></li>
                        <li><a href="product.php">Tất cả điện thoại</a></li>
                        <li><a href="news.php">Tin tức công nghệ</a></li>
                    </ul>
                </div>
                
                <!-- Services Column (Accordion on Mobile) -->
                <div class="footer-col accordion-item-mobile">
                    <div class="footer-title-wrapper d-flex justify-content-between align-items-center">
                        <h6 class="footer-title mb-0">Dịch vụ</h6>
                        <i class="bi bi-plus-lg d-md-none toggle-icon"></i>
                    </div>
                    <ul class="footer-links p-0 list-unstyled small accordion-content-mobile">
                        <li><a href="warranty.php">Chính sách bảo hành</a></li>
                        <li><a href="track_order.php">Tra cứu đơn hàng</a></li>
                        <li><a href="#">Vận chuyển & Giao nhận</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>
                
                <!-- Contact Column (Accordion on Mobile) -->
                <div class="footer-col accordion-item-mobile">
                    <div class="footer-title-wrapper d-flex justify-content-between align-items-center">
                        <h6 class="footer-title mb-0">Kết nối</h6>
                        <i class="bi bi-plus-lg d-md-none toggle-icon"></i>
                    </div>
                    <ul class="footer-links p-0 list-unstyled small accordion-content-mobile">
                        <li class="contact-info"><i class="bi bi-geo-alt me-2 text-primary"></i> Chư Sê, Gia Lai</li>
                        <li class="contact-info"><i class="bi bi-telephone me-2 text-primary"></i> 1800 1234</li>
                        <li class="contact-info"><i class="bi bi-envelope me-2 text-primary"></i> contact@nhkmobile.vn</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom-new pt-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <p class="mb-0 text-muted small">&copy; 2026 NHK Mobile. Bản quyền thiết kế bởi NHK Team.</p>
                <div class="d-flex gap-4">
                    <a href="#" class="text-decoration-none text-muted small hover-primary">Quyền riêng tư</a>
                    <a href="#" class="text-decoration-none text-muted small hover-primary">Điều khoản</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Footer Mobile Accordion Handler -->
    <script>
        document.querySelectorAll('.accordion-item-mobile').forEach(item => {
            const wrapper = item.querySelector('.footer-title-wrapper');
            wrapper.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    item.classList.toggle('active');
                    const icon = item.querySelector('.toggle-icon');
                    if (item.classList.contains('active')) {
                        icon.classList.replace('bi-plus-lg', 'bi-dash-lg');
                    } else {
                        icon.classList.replace('bi-dash-lg', 'bi-plus-lg');
                    }
                }
            });
        });
    </script>

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
