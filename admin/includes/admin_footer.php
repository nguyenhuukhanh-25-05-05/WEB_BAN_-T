    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /**
         * NHK Mobile - Administrative Sidecar Logic
         * 
         * Description: Handles sidebar toggle, mobile offcanvas drawer 
         * transitions, and automatic layout synchronization on resize.
         * 
         * Author: NguyenHuuKhanh
         * Version: 2.1
         * Date: 2026-04-08
         */
        const sidebarMenu = document.getElementById('sidebarMenu');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarClose = document.getElementById('sidebarClose');

        function toggleSidebar() {
            if (sidebarMenu) sidebarMenu.classList.toggle('show');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
            document.body.classList.toggle('overflow-hidden'); // Disable scroll when menu is open
        }

        if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);
        if (sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);

        // Auto-close sidebar on window resize if switching to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992 && sidebarMenu && sidebarMenu.classList.contains('show')) {
                toggleSidebar();
            }
        });
    </script>
</body>
</html>
