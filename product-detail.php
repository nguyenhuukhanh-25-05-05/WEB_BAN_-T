<?php 
session_start();
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

$pageTitle = "NHK Mobile | " . $product['name'];
$basePath = "";
include 'includes/header.php';
?>

    <main class="py-5 mt-5">
        <div class="container px-xl-5">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="product-detail-img-wrapper bg-white p-5 rounded-4 shadow-sm text-center">
                        <img src="assets/images/<?php echo $product['image']; ?>" class="img-fluid" alt="<?php echo $product['name']; ?>" onerror="this.src='https://via.placeholder.com/600x700?text=Phone'">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ps-lg-5">
                        <nav aria-label="breadcrumb" class="mb-4">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="product.php" class="text-decoration-none">Sản phẩm</a></li>
                                <li class="breadcrumb-item active"><?php echo $product['category']; ?></li>
                            </ol>
                        </nav>
                        
                        <h1 class="display-5 fw-bold mb-3"><?php echo $product['name']; ?></h1>
                        <p class="h3 text-primary fw-bold mb-4"><?php echo number_format($product['price'], 0, ',', '.'); ?>₫</p>
                        
                        <div class="mb-5">
                            <h6 class="fw-bold mb-3 text-uppercase small letter-spacing">Mô tả sản phẩm</h6>
                            <p class="text-secondary leading-relaxed">
                                <?php echo nl2br($product['description'] ? $product['description'] : 'Sản phẩm chính hãng với hiệu năng mạnh mẽ.'); ?>
                            </p>
                        </div>

                        <div class="d-grid gap-3">
                            <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn btn-dark btn-lg rounded-pill py-3 fw-bold text-center text-white">Thêm vào giỏ hàng</a>
                            <a href="cart.php?add=<?php echo $product['id']; ?>&installment=1" class="btn btn-outline-dark btn-lg rounded-pill py-3 fw-bold text-center">Mua trả góp 0%</a>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Bắt đầu Phần Đánh giá / Bình luận -->
                <hr class="my-5 border-light">
                <div class="row w-100 mx-0 mt-4">
                    <div class="col-12">
                        <h3 class="fw-bold mb-4">Đánh giá sản phẩm</h3>
                        <div class="d-flex align-items-center mb-4">
                            <h1 class="display-3 fw-bold me-3 mb-0" id="avg-rating">0.0</h1>
                            <div>
                                <div class="text-warning fs-4 mb-1" id="star-rating">
                                    <i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i><i class="bi bi-star"></i>
                                </div>
                                <p class="text-secondary mb-0" id="total-reviews">0 đánh giá</p>
                            </div>
                        </div>
                        
                        <!-- Form Add Review -->
                        <div class="card bg-light border-0 mb-5 rounded-4">
                            <div class="card-body p-4 p-md-5">
                                <h5 class="fw-bold mb-3">Viết đánh giá của bạn</h5>
                                <form id="review-form">
                                    <input type="hidden" id="product_id" value="<?php echo $product['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label text-secondary fw-semibold">Đánh giá sao</label>
                                        <div class="rating-select text-warning fs-4" style="cursor: pointer;">
                                            <i class="bi bi-star rating-star" data-value="1"></i>
                                            <i class="bi bi-star rating-star" data-value="2"></i>
                                            <i class="bi bi-star rating-star" data-value="3"></i>
                                            <i class="bi bi-star rating-star" data-value="4"></i>
                                            <i class="bi bi-star rating-star" data-value="5"></i>
                                        </div>
                                        <input type="hidden" id="rating_val" value="5">
                                    </div>
                                    <?php if(!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])): ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <input type="text" class="form-control" id="reviewer_name" placeholder="Tên của bạn *" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <input type="email" class="form-control" id="reviewer_email" placeholder="Email (không bắt buộc)">
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="review_title" placeholder="Tiêu đề đánh giá (không bắt buộc)">
                                    </div>
                                    <div class="mb-3">
                                        <textarea class="form-control" id="review_content" rows="3" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm *" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-dark px-5 py-2 rounded-pill fw-medium">Gửi đánh giá</button>
                                    <div id="review-msg" class="mt-3 fw-medium"></div>
                                </form>
                            </div>
                        </div>

                        <!-- List Reviews -->
                        <div id="reviews-list">
                            <div class="text-center py-4"><div class="spinner-border text-secondary" role="status"></div></div>
                        </div>
                        
                        <div class="text-center mt-4 mb-5">
                            <button id="load-more-btn" class="btn btn-outline-dark rounded-pill px-4 d-none fw-medium">Xem thêm đánh giá</button>
                        </div>
                    </div>
                </div>

                <style>
                .text-warning, .rating-star.bi-star-fill, #star-rating .bi-star-fill, #star-rating .bi-star-half { color: #ffbc00 !important; }
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
                            list.innerHTML = '<p class="text-center text-muted border rounded p-4 bg-light">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!</p>';
                            return;
                        }
                        
                        reviews.forEach(r => {
                            let stars = '';
                            for(let i=0; i<5; i++) {
                                stars += i < r.rating ? '<i class="bi bi-star-fill text-warning"></i> ' : '<i class="bi bi-star text-warning"></i> ';
                            }
                            
                            const html = `
                                <div class="border-bottom py-4">
                                    <div class="d-flex mb-3">
                                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 48px; height: 48px; font-size: 20px;">
                                            ${r.avatar_letter}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">${r.reviewer_name} ${r.verified_purchase ? '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 ms-2" style="font-size: 11px;"><i class="bi bi-check-circle-fill me-1"></i>Đã mua hàng tại NHK</span>' : ''}</h6>
                                            <div class="small text-muted">${stars} <span class="ms-2"><i class="bi bi-clock me-1"></i>${r.date_formatted}</span></div>
                                        </div>
                                    </div>
                                    ${r.title ? `<h6 class="fw-bold mb-2">${r.title}</h6>` : ''}
                                    <p class="mb-0 text-secondary" style="line-height: 1.6;">${r.content}</p>
                                </div>
                            `;
                            list.insertAdjacentHTML('beforeend', html);
                        });
                    };
                    
                    document.getElementById('review-form').addEventListener('submit', async(e) => {
                        e.preventDefault();
                        const msg = document.getElementById('review-msg');
                        const btn = e.target.querySelector('button[type="submit"]');
                        
                        btn.disabled = true;
                        msg.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div> Đang gửi...';
                        
                        const payload = {
                            product_id: parseInt(productId),
                            rating: parseInt(document.getElementById('rating_val').value),
                            title: document.getElementById('review_title').value,
                            content: document.getElementById('review_content').value
                        };
                        
                        const nameEl = document.getElementById('reviewer_name');
                        if(nameEl) payload.reviewer_name = nameEl.value;
                        const emailEl = document.getElementById('reviewer_email');
                        if(emailEl) payload.reviewer_email = emailEl.value;
                        
                        try {
                            const res = await fetch('api/reviews.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(payload)
                            });
                            const data = await res.json();
                            if(data.success) {
                                msg.innerHTML = `<div class="text-success"><i class="bi bi-check-circle-fill me-1"></i> ${data.message}</div>`;
                                e.target.reset();
                                updateStars(5);
                                loadReviews(1);
                                setTimeout(() => { msg.innerHTML = ''; }, 5000);
                            } else {
                                msg.innerHTML = `<div class="text-danger"><i class="bi bi-exclamation-circle-fill me-1"></i> ${data.error}</div>`;
                            }
                        } catch(err) {
                            msg.innerHTML = `<div class="text-danger"><i class="bi bi-x-circle-fill me-1"></i> Lỗi kết nối máy chủ</div>`;
                        } finally {
                            btn.disabled = false;
                        }
                    });

                    loadReviews();
                });
                </script>
                <!-- Kết thúc Phần Đánh giá / Bình luận -->
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>
