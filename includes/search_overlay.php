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
    backdrop-filter: blur(var(--glass-blur));
    -webkit-backdrop-filter: blur(var(--glass-blur));
    z-index: 10000;
    overflow-y: auto;
}

.search-overlay-container {
    position: relative;
    width: 100%;
    min-height: 100vh;
}

.btn-close-search {
    position: absolute;
    top: 40px;
    right: 40px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.1);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition-smooth);
    z-index: 10001;
}

.btn-close-search:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: rotate(90deg) scale(1.1);
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

.suggestion-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--radius-lg);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
    text-decoration: none !important;
    transition: var(--transition-smooth);
}

.suggestion-card:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.suggestion-img {
    width: 80px;
    height: 80px;
    object-fit: contain;
    background: #fff;
    padding: 10px;
    border-radius: var(--radius-md);
}

.suggestion-info {
    flex: 1;
}

.suggestion-name {
    color: #fff;
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 4px;
}

.suggestion-price {
    color: var(--apple-blue);
    font-weight: 600;
    font-size: 1rem;
}

.tracking-widest { letter-spacing: 0.25em; }

.btn-premium-glass {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.8);
    transition: var(--transition-smooth);
}

.btn-premium-glass:hover {
    background: var(--apple-blue);
    color: #fff;
    border-color: var(--apple-blue);
    transform: translateY(-3px);
}
</style>
