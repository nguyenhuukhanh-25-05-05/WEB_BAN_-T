document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo các phần tử giao diện của bộ tìm kiếm (Overlay Search)
    const searchTrigger = document.getElementById('searchTrigger');
    const searchOverlay = document.getElementById('searchOverlay');
    const closeSearch = document.getElementById('closeSearch');
    const searchInput = document.getElementById('searchInputMain');
    const searchResults = document.getElementById('searchResults');
    const quickSuggestions = document.getElementById('quickSuggestions');

    if (!searchTrigger || !searchOverlay) return;

    /**
     * Mở khung tìm kiếm toàn màn hình khi nhấn vào biểu tượng tìm kiếm.
     */
    searchTrigger.addEventListener('click', function(e) {
        e.preventDefault();
        searchOverlay.classList.remove('d-none'); // Hiện Overlay
        document.body.style.overflow = 'hidden'; // Khóa cuộn trang chính
        setTimeout(() => searchInput.focus(), 100); // Tự động tập trung vào ô nhập liệu
    });

    /**
     * Đóng khung tìm kiếm và khôi phục trạng thái ban đầu.
     */
    function closeOverlay() {
        searchOverlay.classList.add('d-none'); // Ẩn Overlay
        document.body.style.overflow = ''; // Mở lại cuộn trang
        searchInput.value = ''; // Xóa nội dung tìm kiếm cũ
        searchResults.classList.add('d-none'); // Ẩn kết quả tìm kiếm
        quickSuggestions.classList.remove('d-none'); // Hiện lại gợi ý nhanh
    }

    // Sự kiện đóng khi nhấn nút X
    if (closeSearch) closeSearch.addEventListener('click', closeOverlay);
    
    /**
     * Đóng khung tìm kiếm khi nhấn phím ESC (Escape).
     */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !searchOverlay.classList.contains('d-none')) {
            closeOverlay();
        }
    });

    /**
     * Xử lý logic Tìm kiếm trực tiếp (Live Search) với kỹ thuật Debounce.
     * Tránh gửi yêu cầu liên tục lên máy chủ khi người dùng đang gõ phím nhanh.
     */
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        // Nếu chuỗi tìm kiếm quá ngắn, quay lại hiển thị Gợi ý nhanh
        if (query.length < 1) {
            searchResults.classList.add('d-none');
            quickSuggestions.classList.remove('d-none');
            return;
        }

        // Đợi 300ms sau khi người dùng dừng gõ mới bắt đầu tìm kiếm
        debounceTimer = setTimeout(() => {
            fetch(`${SEARCH_API_URL}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    renderResults(data); // Hiển thị kết quả tìm được
                })
                .catch(err => console.error('Lỗi khi tìm kiếm:', err));
        }, 300);
    });

    /**
     * Hiển thị danh sách kết quả tìm kiếm (Sản phẩm hoặc Tin tức) lên giao diện.
     * @param {array} data Mảng các đối tượng kết quả từ API
     */
    function renderResults(data) {
        // Trường hợp không tìm thấy kết quả nào
        if (data.length === 0) {
            searchResults.innerHTML = '<div class="col-12 text-center py-5 text-secondary-light">Không tìm thấy kết quả nào cho từ khóa này...</div>';
            searchResults.classList.remove('d-none');
            quickSuggestions.classList.add('d-none');
            return;
        }

        let html = '';
        data.forEach(item => {
            // Tùy biến kiểu hiển thị giá tùy theo loại kết quả (Sản phẩm hay Tin tức)
            let priceClass = item.type === 'news' ? 'text-secondary small mt-1' : 'suggestion-price';
            
            html += `
                <div class="col-md-6 col-lg-4 animate-reveal">
                    <a href="${item.url}" class="suggestion-card">
                        <img src="assets/images/${item.image}" class="suggestion-img" style="${item.type === 'news' ? 'object-fit: cover;' : 'object-fit: contain;'}" onerror="this.src='https://placehold.co/100x100?text=Result'">
                        <div class="suggestion-info" style="overflow: hidden;">
                            <div class="suggestion-name text-truncate">${item.name}</div>
                            <div class="small ${item.type === 'news' ? 'text-primary fw-medium' : 'text-secondary'} mb-1">${item.category}</div>
                            <div class="${priceClass} text-truncate">${item.formatted_price}</div>
                        </div>
                    </a>
                </div>
            `;
        });

        // Cập nhật vùng hiển thị kết quả
        searchResults.innerHTML = html;
        searchResults.classList.remove('d-none');
        quickSuggestions.classList.add('d-none');
    }
});
