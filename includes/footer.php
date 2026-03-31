    <footer class="footer-modern">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Sản phẩm</h4>
                    <ul>
                        <li><a href="<?php echo $basePath; ?>product.php?category=Xiaomi">Xiaomi</a></li>
                        <li><a href="<?php echo $basePath; ?>product.php?category=Oppo">Oppo</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Hỗ trợ</h4>
                    <ul>
                        <li><a href="<?php echo $basePath; ?>warranty.php">Tra cứu bảo hành</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Giao hàng & Thanh toán</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>NHK Mobile</h4>
                    <ul>
                        <li><a href="#">Về chúng tôi</a></li>
                        <li><a href="<?php echo $basePath; ?>news.php">Tin tức công nghệ</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Liên hệ</h4>
                    <ul>
                        <li><a href="tel:0333427187">Hotline: 0333 427 187</a></li>
                        <li><a href="#">Địa chỉ: 123 Cầu Giấy, Hà Nội</a></li>
                        <li><a href="mailto:support@nhkmobile.vn">support@nhkmobile.vn</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="footer-copy">
                    &copy; 2026 NHK Mobile. Bản quyền thuộc về Nguyễn Hữu Khánh.
                </div>
                <div class="footer-socials">
                    <a href="https://www.facebook.com/nguyen.huu.khanh.250505" target="_blank"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/nguyenhuukhanh1893/" target="_blank"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.tiktok.com/@nguyenhuukhanh_0" target="_blank"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Contact Widget -->
    <div class="floating-widget" id="contactWidget">
        <button class="widget-toggle" aria-label="Toggle Menu" onclick="document.getElementById('contactWidget').classList.toggle('active')">
            <span class="toggle-icon">‹</span>
        </button>
        <div class="widget-menu">
            <a href="https://zalo.me/0333427187" target="_blank" class="widget-item zalo" title="Zalo Chat">
                <img src="<?php echo $basePath; ?>assets/images/zalo.jpg" alt="Zalo" style="width: 24px; border-radius: 4px;">
                <span>Zalo</span>
            </a>
            <a href="javascript:void(0)" class="widget-item ai" title="AI Chat" id="aiChatToggle">
                <i class="bi bi-robot fs-4"></i>
                <span>AI Chat</span>
            </a>
        </div>
    </div>

    <!-- AI Chat Window -->
    <div class="ai-chat-window" id="aiChatWindow">
        <div class="ai-chat-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-robot fs-4"></i>
                <span class="fw-bold">Trợ lý ảo NHK Mobile</span>
            </div>
            <button class="btn-close btn-close-white" id="aiChatClose"></button>
        </div>
        <div class="ai-chat-body" id="aiChatBody">
            <div class="ai-message bg-light p-2 rounded-3 mb-2 small">
                Chào bạn! Tôi là trợ lý ảo của NHK Mobile. Tôi có thể giúp gì cho bạn?
            </div>
        </div>
        <div class="ai-chat-footer p-2 border-top bg-white">
            <div class="input-group">
                <input type="text" id="aiChatInput" class="form-control border-0 shadow-none" placeholder="Nhập câu hỏi...">
                <button class="btn btn-primary rounded-circle ms-2" id="aiChatSend">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $basePath; ?>assets/js/ui.js"></script>
    <script src="<?php echo $basePath; ?>assets/js/ai-chat.js"></script>
    <script src="<?php echo $basePath; ?>assets/js/search-overlay.js"></script>

    <script>
    // Premium reveal animations on scroll
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-reveal, .animate-fade-in').forEach(el => {
            observer.observe(el);
        });

        // Navbar scroll effect
        const navbar = document.querySelector('.navbar-modern');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    });
    </script>

    <!-- Global Styles for Widgets (Kept minimal here, most in style.css) -->
    <style>
    .floating-widget { position: fixed; right: 0; top: 70%; transform: translateY(-50%); z-index: 9999; display: flex; align-items: center; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .floating-widget:not(.active) { transform: translate(100%, -50%); }
    .widget-toggle { background: var(--bg-white); color: var(--primary-black); width: 40px; height: 50px; border: 1px solid var(--border-light); border-right: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 24px; position: absolute; left: -40px; border-radius: 10px 0 0 10px; box-shadow: var(--shadow-md); }
    .widget-menu { background: var(--glass-bg); backdrop-filter: blur(var(--glass-blur)); border: 1px solid var(--border-light); border-right: none; border-radius: 20px 0 0 20px; padding: 15px; display: flex; flex-direction: column; gap: 10px; box-shadow: var(--shadow-lg); }
    .widget-item { display: flex; flex-direction: column; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 12px; transition: all 0.3s; gap: 4px; color: var(--primary-black); }
    .widget-item span { font-size: 10px; font-weight: 600; }
    .widget-item:hover { background: rgba(0,0,0,0.05); transform: scale(1.05); }
    
    .ai-chat-window { position: fixed; bottom: 100px; right: 80px; width: 350px; height: 500px; background: #fff; z-index: 10000; display: none; flex-direction: column; border-radius: 20px; overflow: hidden; box-shadow: var(--shadow-lg); border: 1px solid var(--border-light); }
    .ai-chat-window.active { display: flex; animation: slideUp 0.3s ease; }
    .ai-chat-header { background: var(--primary-black); color: #fff; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
    .ai-chat-body { flex-grow: 1; overflow-y: auto; padding: 15px; display: flex; flex-direction: column; gap: 10px; }
    .ai-message { align-self: flex-start; max-width: 85%; background: #f0f0f2; padding: 10px 15px; border-radius: 15px 15px 15px 2px; font-size: 14px; }
    .user-message { align-self: flex-end; max-width: 85%; background: var(--apple-blue); color: #fff; padding: 10px 15px; border-radius: 15px 15px 2px 15px; font-size: 14px; }
    
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @media (max-width: 768px) { .ai-chat-window { width: calc(100% - 40px); right: 20px; bottom: 80px; height: 450px; } .floating-widget { top: auto; bottom: 120px; } }
    </style>

    <!-- Bootstrap Toast Container -->
    <div class="toast-container position-fixed bottom-0 start-0 p-3" style="z-index: 11000;">
        <div id="liveToast" class="toast align-items-center text-white bg-dark border-0 rounded-4 shadow-lg p-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-3">
                    <div id="toastIcon" class="bg-primary p-2 rounded-3 text-white">
                        <i class="bi bi-cart-plus-fill"></i>
                    </div>
                    <div>
                        <strong id="toastTitle" class="d-block">Thông báo</strong>
                        <span id="toastMessage">Sản phẩm đã được thêm vào giỏ hàng.</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
</body>
</html>
