<?php 
require_once 'includes/db.php';
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header("Location: product.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Sản phẩm không tồn tại!");
}

$pageTitle = "NHK Mobile | " . e($product['name']);
$basePath = "";
include 'includes/header.php';
?>

<main class="section-padding mt-5">
    <div class="container">
        <div class="row g-5">
            <!-- Product Image -->
            <div class="col-lg-6">
                <div class="bg-light rounded-4 p-5 text-center">
                    <img src="assets/images/<?php echo e($product['image']); ?>" class="img-fluid" alt="<?php echo e($product['name']); ?>" style="max-height: 500px; object-fit: contain;" onerror="this.src='https://placehold.co/600x700/f5f5f7/1d1d1f?text=Phone'">
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="ps-lg-5">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="product.php" class="text-secondary">Sản phẩm</a></li>
                            <li class="breadcrumb-item active"><?php echo e($product['category']); ?></li>
                        </ol>
                    </nav>
                    
                    <h1 class="hero-title text-start mb-3" style="font-size: 48px;"><?php echo e($product['name']); ?></h1>
                    <div class="h3 text-primary fw-bold mb-4"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</div>
                    
                    <div class="mb-5">
                        <h6 class="text-secondary small fw-bold text-uppercase mb-3">Mô tả sản phẩm</h6>
                        <p class="hero-subtitle text-start m-0" style="font-size: 16px;">
                            <?php echo nl2br(e($product['description'] ? $product['description'] : 'Sản phẩm chính hãng với hiệu năng mạnh mẽ, thiết kế sang trọng và trải nghiệm người dùng tuyệt vời.')); ?>
                        </p>
                    </div>

                    <div class="d-grid gap-3">
                        <button class="btn-primary-apple btn-lg py-3 btn-add-to-cart-ajax" data-product-id="<?php echo e($product['id']); ?>">Thêm vào giỏ hàng</button>
                        <button class="btn-p-view btn-lg py-3 btn-add-to-cart-ajax" data-product-id="<?php echo e($product['id']); ?>" data-installment="1">Mua trả góp 0%</button>
                    </div>

                    <!-- Trust Badges Mini -->
                    <div class="row mt-5 g-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-truck text-primary fs-4"></i>
                                <span class="small fw-medium">Giao hàng miễn phí</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-primary fs-4"></i>
                                <span class="small fw-medium">Bảo hành 12 tháng</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="mt-5 pt-5 border-top">
            <h2 class="section-title text-start mb-5">Đánh giá sản phẩm</h2>
            <div class="row">
                <div class="col-lg-4 mb-5">
                    <div class="bg-light rounded-4 p-4 text-center">
                        <h1 class="display-3 fw-bold mb-0" id="avg-rating">0.0</h1>
                        <div class="text-warning fs-4 mb-2" id="star-rating">
                            <i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i>
                        </div>
                        <p class="text-secondary mb-0" id="total-reviews">0 đánh giá</p>
                    </div>
                </div>

                <div class="col-lg-8">
                    <!-- Add Review Form -->
                    <div class="bg-white border rounded-4 p-4 mb-5">
                        <h5 class="fw-bold mb-4">Viết đánh giá của bạn</h5>
                        <form id="review-form">
                            <input type="hidden" id="product_id" value="<?php echo $product['id']; ?>">
                            <div class="mb-4">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Mức độ hài lòng</label>
                                <div class="rating-select text-warning fs-3" style="cursor: pointer;">
                                    <i class="bi bi-star rating-star" data-value="1"></i>
                                    <i class="bi bi-star rating-star" data-value="2"></i>
                                    <i class="bi bi-star rating-star" data-value="3"></i>
                                    <i class="bi bi-star rating-star" data-value="4"></i>
                                    <i class="bi bi-star rating-star" data-value="5"></i>
                                </div>
                                <input type="hidden" id="rating_val" value="5">
                            </div>
                            <?php if(!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])): ?>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control rounded-pill bg-light border-0" id="reviewer_name" placeholder="Tên của bạn *" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" class="form-control rounded-pill bg-light border-0" id="reviewer_email" placeholder="Email (không bắt buộc)">
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <input type="text" class="form-control rounded-pill bg-light border-0" id="review_title" placeholder="Tiêu đề đánh giá">
                            </div>
                            <div class="mb-4">
                                <textarea class="form-control bg-light border-0 rounded-4" id="review_content" rows="4" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm *" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-dark px-5 py-3 rounded-pill fw-bold">Gửi đánh giá</button>
                            <div id="review-msg" class="mt-3"></div>
                        </form>
                    </div>

                    <!-- Reviews List -->
                    <div id="reviews-list">
                        <div class="text-center py-4"><div class="spinner-border text-secondary" role="status"></div></div>
                    </div>
                    
                    <div class="text-center mt-4 mb-5">
                        <button id="load-more-btn" class="btn btn-outline-dark rounded-pill px-4 d-none">Xem thêm đánh giá</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.text-warning { color: #ffbc00 !important; }
.rating-star.bi-star-fill { color: #ffbc00 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const productId = document.getElementById('product_id').value;
    let currentPage = 1;
    const limit = 5;
    
    const stars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('rating_val');
    
    function updateStars(val) {
        stars.forEach(star => {
            if(parseInt(star.dataset.value) <= val) {
                star.classList.remove('bi-star');
                star.classList.add('bi-star-fill');
            } else {
                star.classList.remove('bi-star-fill');
                star.classList.add('bi-star');
            }
        });
    }
    
    updateStars(5);
    
    stars.forEach(star => {
        star.addEventListener('click', (e) => {
            const val = parseInt(e.target.dataset.value);
            ratingInput.value = val;
            updateStars(val);
        });
    });

    const loadReviews = async (page = 1) => {
        try {
            const res = await fetch(`api/reviews.php?id=${productId}&page=${page}&limit=${limit}`);
            const data = await res.json();
            
            if(data.success) {
                renderReviews(data.reviews, page === 1);
                updateMeta(data.meta);
                
                const loadMoreBtn = document.getElementById('load-more-btn');
                if(data.meta.page < data.meta.total_pages) {
                    loadMoreBtn.classList.remove('d-none');
                    loadMoreBtn.onclick = () => loadReviews(page + 1);
                } else {
                    loadMoreBtn.classList.add('d-none');
                }
            }
        } catch(err) {
            console.error(err);
        }
    };
    
    const updateMeta = (meta) => {
        document.getElementById('avg-rating').innerText = meta.avg_rating.toFixed(1);
        document.getElementById('total-reviews').innerText = `${meta.total} đánh giá`;
        
        let starHtml = '';
        const fullStars = Math.floor(meta.avg_rating);
        const hasHalf = meta.avg_rating - fullStars >= 0.5;
        for(let i=0; i<fullStars; i++) starHtml += '<i class="bi bi-star-fill"></i> ';
        if(hasHalf) starHtml += '<i class="bi bi-star-half"></i> ';
        const emptyStars = 5 - fullStars - (hasHalf ? 1 : 0);
        for(let i=0; i<emptyStars; i++) starHtml += '<i class="bi bi-star"></i> ';
        document.getElementById('star-rating').innerHTML = starHtml;
    };
    
    const renderReviews = (reviews, clear = false) => {
        const list = document.getElementById('reviews-list');
        if(clear) list.innerHTML = '';
        if(reviews.length === 0 && clear) {
            list.innerHTML = '<p class="text-center text-muted border rounded-4 p-4 bg-light">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!</p>';
            return;
        }
        
        reviews.forEach(r => {
            const date = new Date(r.created_at).toLocaleDateString('vi-VN');
            let stars = '';
            for(let i=1; i<=5; i++) stars += `<i class="bi bi-star${i <= r.rating ? '-fill' : ''}"></i> `;
            
            const div = document.createElement('div');
            div.className = 'mb-4 pb-4 border-bottom';
            div.innerHTML = `
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold">${r.reviewer_name}</span>
                    <span class="text-secondary small">${date}</span>
                </div>
                <div class="text-warning mb-2 small">${stars}</div>
                ${r.title ? `<h6 class="fw-bold mb-1">${r.title}</h6>` : ''}
                <p class="text-secondary mb-0 small">${r.content}</p>
            `;
            list.appendChild(div);
        });
    };

    loadReviews();

    // Submit Review
    const form = document.getElementById('review-form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = document.getElementById('review-msg');
        msg.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div> Đang gửi...';
        
        const body = new FormData();
        body.append('product_id', productId);
        body.append('rating', ratingInput.value);
        body.append('content', document.getElementById('review_content').value);
        body.append('title', document.getElementById('review_title').value);
        
        const nameEl = document.getElementById('reviewer_name');
        if(nameEl) body.append('reviewer_name', nameEl.value);
        const emailEl = document.getElementById('reviewer_email');
        if(emailEl) body.append('reviewer_email', emailEl.value);
        
        try {
            const res = await fetch('api/reviews.php', { method: 'POST', body });
            const data = await res.json();
            if(data.success) {
                msg.innerHTML = '<span class="text-success">Cảm ơn bạn đã đánh giá!</span>';
                form.reset();
                updateStars(5);
                loadReviews(1);
            } else {
                msg.innerHTML = `<span class="text-danger">${data.message}</span>`;
            }
        } catch(err) {
            msg.innerHTML = '<span class="text-danger">Đã có lỗi xảy ra. Vui lòng thử lại.</span>';
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
