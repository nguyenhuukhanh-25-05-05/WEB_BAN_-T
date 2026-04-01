document.addEventListener('DOMContentLoaded', function() {
    const searchTrigger = document.getElementById('searchTrigger');
    const searchOverlay = document.getElementById('searchOverlay');
    const closeSearch = document.getElementById('closeSearch');
    const searchInput = document.getElementById('searchInputMain');
    const searchResults = document.getElementById('searchResults');
    const quickSuggestions = document.getElementById('quickSuggestions');

    if (!searchTrigger || !searchOverlay) return;

    // Open Search Overlay
    searchTrigger.addEventListener('click', function(e) {
        e.preventDefault();
        searchOverlay.classList.remove('d-none');
        document.body.style.overflow = 'hidden'; // Lock scroll
        setTimeout(() => searchInput.focus(), 100);
    });

    // Close Search Overlay
    function closeOverlay() {
        searchOverlay.classList.add('d-none');
        document.body.style.overflow = ''; // Unlock scroll
        searchInput.value = '';
        searchResults.classList.add('d-none');
        quickSuggestions.classList.remove('d-none');
    }

    closeSearch.addEventListener('click', closeOverlay);
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !searchOverlay.classList.contains('d-none')) {
            closeOverlay();
        }
    });

    // Live Search Logic
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 1) {
            searchResults.classList.add('d-none');
            quickSuggestions.classList.remove('d-none');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`${SEARCH_API_URL}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    renderResults(data);
                })
                .catch(err => console.error('Search error:', err));
        }, 300);
    });

    function renderResults(data) {
        if (data.length === 0) {
            searchResults.innerHTML = '<div class="col-12 text-center py-5 text-secondary-light">Không tìm thấy kết quả nào...</div>';
            searchResults.classList.remove('d-none');
            quickSuggestions.classList.add('d-none');
            return;
        }

        let html = '';
        data.forEach(item => {
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

        searchResults.innerHTML = html;
        searchResults.classList.remove('d-none');
        quickSuggestions.classList.add('d-none');
    }
});
