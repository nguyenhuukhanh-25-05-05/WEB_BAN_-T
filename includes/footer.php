<?php
/**
 * NHK Mobile - Global Footer System
 * 
 * Description: Contains the multi-column footer, social links, 
 * legal information, search overlay inclusion, and core JS logic.
 * 
 * Author: NguyenHuuKhanh
 * Version: 2.2
 * Date: 2026-04-08
 */
?>
<footer class="footer-new bg-light mt-5 border-top">
        <div class="container-wide py-5">
            <div class="footer-grid">
                <!-- Brand Section -->
                <div class="footer-brand">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-4">
                        <div class="logo-box me-3">K</div>
                        <span class="fw-800 fs-5 tracking-tight text-main">NHK MOBILE</span>
                    </div>
                    <p class="text-secondary small mb-4" style="max-width: 300px;">Đại lý ủy quyền chính thức của Apple tại Việt Nam. Chúng tôi mang đến những trải nghiệm công nghệ đỉnh cao cùng dịch vụ hậu mãi chuẩn 5 sao.</p>
                    <div class="d-flex justify-content-center justify-content-md-start gap-3">
                        <a href="#" class="social-icon" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-icon" title="TikTok"><i class="bi bi-tiktok"></i></a>
                        <a href="#" class="social-icon" title="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                
                <!-- Explorar Column -->
                <div class="footer-col accordion-item-mobile">
                    <div class="footer-title-wrapper d-flex justify-content-between align-items-center">
                        <h6 class="footer-title">Khám phá</h6>
                        <i class="bi bi-plus-lg d-md-none toggle-icon"></i>
                    </div>
                    <ul class="footer-links list-unstyled accordion-content-mobile">
                        <li><a href="product.php?category=Apple">iPhone Series</a></li>
                        <li><a href="product.php?category=Samsung">Samsung Galaxy</a></li>
                        <li><a href="product.php">Phụ kiện cao cấp</a></li>
                        <li><a href="news.php">Tin tức công nghệ</a></li>
                    </ul>
                </div>
                
                <!-- Services Column -->
                <div class="footer-col accordion-item-mobile">
                    <div class="footer-title-wrapper d-flex justify-content-between align-items-center">
                        <h6 class="footer-title">Dịch vụ</h6>
                        <i class="bi bi-plus-lg d-md-none toggle-icon"></i>
                    </div>
                    <ul class="footer-links list-unstyled accordion-content-mobile">
                        <li><a href="warranty.php">Trung tâm Bảo hành</a></li>
                        <li><a href="track_order.php"><?php echo isset($_SESSION['user_id']) ? 'Đơn hàng của tôi' : 'Tình trạng đơn hàng'; ?></a></li>
                        <li><a href="#">Giao hàng tận nơi</a></li>
                        <li><a href="#">Hình thức thanh toán</a></li>
                    </ul>
                </div>
                
                <!-- About Column -->
                <div class="footer-col accordion-item-mobile">
                    <div class="footer-title-wrapper d-flex justify-content-between align-items-center">
                        <h6 class="footer-title">Về NHK Mobile</h6>
                        <i class="bi bi-plus-lg d-md-none toggle-icon"></i>
                    </div>
                    <ul class="footer-links list-unstyled accordion-content-mobile">
                        <li><a href="#">Giới thiệu công ty</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Liên hệ hợp tác</a></li>
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
