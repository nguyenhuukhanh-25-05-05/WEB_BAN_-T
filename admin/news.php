<?php
// Bắt đầu phiên làm việc
require_once 'admin_auth.php';
require_once '../includes/db.php';

$pageTitle = "Quản lý Tin tức | Admin";
$basePath = "../";

/**
 * 1. XỬ LÝ LOGIC CRUD (THÊM / SỬA / XÓA)
 */

if (isset($_POST['save_news'])) {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $category = $_POST['category'];
    $excerpt = $_POST['excerpt'];
    $content = $_POST['content'];
    $image = "placeholder.png"; // Tạm thời dùng hình mặc định

    if ($id) {
        $sql = "UPDATE news SET title = ?, category = ?, excerpt = ?, content = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $category, $excerpt, $content, $id]);
    } else {
        $sql = "INSERT INTO news (title, category, excerpt, content, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $category, $excerpt, $content, $image]);
    }
    header("Location: news.php?msg=success");
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: news.php?msg=deleted");
    exit;
}

/**
 * 2. LẤY DỮ LIỆU HIỂN THỊ
 */

$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
$newsList = $stmt->fetchAll();

// Xử lý bật Modal Sửa tự động nếu có biến GET edit
$editData = null;
if (isset($_GET['edit'])) {
    $stmtEdit = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmtEdit->execute([$_GET['edit']]);
    $editData = $stmtEdit->fetch();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar text-white d-none d-lg-block">
        <div class="mb-5 px-3">
             <img src="../assets/images/logo-k.svg" height="20" alt="Logo" class="brightness-0 invert opacity-75">
        </div>
        <nav>
            <a href="dashboard.php" class="nav-link-admin"><i class="bi bi-speedometer2"></i> Tổng quan</a>
            <a href="products.php" class="nav-link-admin"><i class="bi bi-box-seam"></i> Sản phẩm</a>
            <a href="orders.php" class="nav-link-admin"><i class="bi bi-receipt"></i> Đơn hàng</a>
            <a href="users.php" class="nav-link-admin"><i class="bi bi-people"></i> Khách hàng</a>
            <a href="warranties.php" class="nav-link-admin"><i class="bi bi-shield-check"></i> Bảo hành IMEI</a>
            <a href="news.php" class="nav-link-admin active"><i class="bi bi-newspaper"></i> Tin tức & Tech</a>
            
            <div class="mt-5 pt-5 border-top border-secondary mx-3">
                 <a href="../index.php" class="nav-link-admin text-info ps-0 mb-2"><i class="bi bi-box-arrow-left"></i> Xem Website</a>
                 <a href="logout.php" class="nav-link-admin text-danger ps-0 small"><i class="bi bi-power"></i> Đăng xuất</a>
            </div>
        </nav>
    </aside>

    <!-- NỘI DUNG CHÍNH -->
    <main class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                 <h2 class="fw-bold mb-1">Quản lý Tin tức</h2>
                 <p class="text-secondary small mb-0">Viết và cập nhật tin công nghệ hàng ngày.</p>
            </div>
            <button class="btn btn-primary shadow-sm px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#newsModal">
                <i class="bi bi-pencil-square me-2"></i> Viết bài mới
            </button>
        </header>

        <div class="content-card shadow-sm border-0 rounded-4 p-4 bg-white">
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4 border-0 rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> Thao tác thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="small text-uppercase text-secondary">
                            <th>Bài viết</th>
                            <th>Danh mục</th>
                            <th>Ngày đăng</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($newsList as $n): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="../assets/images/<?php echo $n['image']; ?>" class="rounded-3 me-3" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='https://placehold.co/60'">
                                    <div>
                                        <div class="fw-bold mb-1 text-dark" style="max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($n['title']); ?></div>
                                        <div class="small text-secondary" style="max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($n['excerpt']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border fw-normal"><?php echo htmlspecialchars($n['category']); ?></span></td>
                            <td class="small text-secondary"><?php echo date('d/m/Y H:i', strtotime($n['created_at'])); ?></td>
                            <td class="text-end">
                                <a href="news.php?edit=<?php echo $n['id']; ?>" class="btn btn-sm btn-light border p-2"><i class="bi bi-pencil text-primary"></i></a>
                                <a href="news.php?delete=<?php echo $n['id']; ?>" class="btn btn-sm btn-light border p-2 text-danger ms-1" onclick="return confirm('Xóa bài viết này vĩnh viễn?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($newsList) === 0): ?>
                        <tr><td colspan="4" class="text-center py-4 text-secondary">Chưa có bài viết nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL THÊM / SỬA BÀI VIẾT -->
    <div class="modal fade <?php echo $editData ? 'show' : ''; ?>" id="newsModal" tabindex="-1" <?php echo $editData ? 'style="display: block; background: rgba(0,0,0,0.5)"' : ''; ?>>
        <div class="modal-dialog modal-lg border-0">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <form action="news.php" method="POST">
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="fw-bold mb-0"><?php echo $editData ? 'Sửa bài viết' : 'Viết bài công nghệ mới'; ?></h5>
                        <a href="news.php" class="btn-close"></a>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <input type="hidden" name="id" value="<?php echo $editData['id'] ?? ''; ?>">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label small fw-bold">Tiêu đề bài viết *</label>
                                <input type="text" name="title" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($editData['title'] ?? ''); ?>" required placeholder="Nhập tiêu đề hấp dẫn...">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">Danh mục *</label>
                                <select name="category" class="form-select bg-light border-0" required>
                                    <option value="Technology" <?php echo (isset($editData['category']) && $editData['category'] == 'Technology') ? 'selected' : ''; ?>>Công nghệ chung</option>
                                    <option value="Apple" <?php echo (isset($editData['category']) && $editData['category'] == 'Apple') ? 'selected' : ''; ?>>Hệ sinh thái Apple</option>
                                    <option value="Samsung" <?php echo (isset($editData['category']) && $editData['category'] == 'Samsung') ? 'selected' : ''; ?>>Hệ sinh thái Samsung</option>
                                    <option value="Tips" <?php echo (isset($editData['category']) && $editData['category'] == 'Tips') ? 'selected' : ''; ?>>Mẹo & Thủ thuật</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Đoạn trích tóm tắt (Excerpt) *</label>
                            <textarea name="excerpt" class="form-control bg-light border-0" rows="2" required placeholder="Tóm tắt nội dung chính trong 1-2 câu..."><?php echo htmlspecialchars($editData['excerpt'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nội dung chi tiết *</label>
                            <textarea name="content" class="form-control bg-light border-0" rows="10" required placeholder="Viết nội dung bài báo tại đây... Hỗ trợ Markdown/HTML cơ bản."><?php echo htmlspecialchars($editData['content'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4">
                        <a href="news.php" class="btn btn-light px-4 rounded-pill">Hủy bỏ</a>
                        <button type="submit" name="save_news" class="btn btn-primary px-4 rounded-pill shadow-sm">Đăng tải bài viết</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
