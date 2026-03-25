<!-- Search Overlay Overlay -->
<div id="searchOverlay" class="search-overlay d-none">
    <div class="search-overlay-container">
        <!-- Close Button -->
        <button id="closeSearch" class="btn-close-search">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="container py-huge">
            <div class="search-input-wrapper animate-reveal">
                <span class="text-secondary-light small fw-bold text-uppercase mb-3 d-block tracking-widest">Tìm kiếm siêu phẩm</span>
                <div class="position-relative">
                    <input type="text" id="searchInputMain" 
                           class="form-control-minimal display-4 fw-800 text-white" 
                           placeholder="Nhập tên máy..." 
                           autocomplete="off">
                    <div class="search-indicator-line"></div>
                </div>
            </div>

            <!-- Live Results Container -->
            <div id="searchResults" class="row mt-5 pt-4 g-4 d-none">
                <!-- Suggestions will be injected here via JS -->
            </div>

            <!-- Quick Suggestions -->
            <div id="quickSuggestions" class="mt-5 pt-4 animate-reveal" style="animation-delay: 0.1s">
                <h6 class="text-secondary small fw-bold text-uppercase mb-4 tracking-widest">Gợi ý nhanh</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="product.php?category=Apple" class="btn btn-premium-glass px-4 py-2 border-0">iPhone 17 Pro Max</a>
                    <a href="product.php?category=Samsung" class="btn btn-premium-glass px-4 py-2 border-0">Samsung S25 Ultra</a>
                    <a href="product.php?category=Xiaomi" class="btn btn-premium-glass px-4 py-2 border-0">Xiaomi Mix Flip</a>
                    <a href="product.php?category=Oppo" class="btn btn-premium-glass px-4 py-2 border-0">Oppo Find X10</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.search-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(30px);
    -webkit-backdrop-filter: blur(30px);
    z-index: 9999;
    overflow-y: auto;
}

.search-overlay-container {
    position: relative;
    width: 100%;
    min-height: 100vh;
}

.btn-close-search {
    position: absolute;
    top: 30px;
    right: 40px;
    background: none;
    border: none;
    color: #86868b;
    font-size: 2rem;
    cursor: pointer;
    transition: all 0.3s;
    z-index: 10000;
}

.btn-close-search:hover {
    color: #fff;
    transform: rotate(90deg);
}

.form-control-minimal {
    background: transparent;
    border: none;
    outline: none !important;
    padding: 20px 0;
    width: 100%;
    color: #fff;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
}

.search-indicator-line {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 2px;
    width: 0;
    background: var(--apple-blue);
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-control-minimal:focus + .search-indicator-line {
    width: 100%;
}

/* Suggestion Cards */
.suggestion-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 20px;
    text-decoration: none !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.suggestion-card:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-5px);
}

.suggestion-img {
    width: 70px;
    height: 70px;
    object-fit: contain;
    background: #000;
    padding: 8px;
    border-radius: 12px;
}

.suggestion-info {
    flex: 1;
}

.suggestion-name {
    color: #fff;
    font-weight: 600;
    margin-bottom: 2px;
}

.suggestion-price {
    color: var(--apple-blue);
    font-weight: 500;
    font-size: 0.9rem;
}

.tracking-widest { letter-spacing: 0.2em; }
</style>
