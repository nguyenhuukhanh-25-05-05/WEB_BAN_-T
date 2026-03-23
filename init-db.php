<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khởi tạo CSDL | NHK Mobile Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: var(--bg-body, #f5f5f7);
            color: var(--text-main, #1d1d1f);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .init-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border: 1px solid rgba(255,255,255,0.5);
            padding: 3rem;
            max-width: 600px;
            width: 100%;
        }
        .status-box {
            background: #fdfdfd;
            border: 1px solid #e1e1e7;
            border-radius: 12px;
            padding: 20px;
            font-family: monospace;
            font-size: 14px;
            color: #515154;
        }
        .text-success-premium { color: #34c759; }
        .text-danger-premium { color: #ff3b30; }
        .text-warning-premium { color: #ff9f0a; }
        .text-primary-premium { color: #0071e3; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="init-card mx-auto">
                <div class="text-center mb-4">
                    <img src="assets/images/logo-k.svg" alt="Logo" style="width: 50px;" class="mb-3">
                    <h2 class="fw-bold mb-1">Cài đặt Cơ sở dữ liệu</h2>
                    <p class="text-secondary small">Hệ thống quản lý NHK Mobile</p>
                </div>

                <div class="status-box mb-4">
                    <?php
                    $isSuccess = false;
                    try {
                        echo "<p class='mb-2'><i class='bi bi-search text-primary-premium me-2'></i> <strong>Đang chuẩn bị thông số kết nối...</strong></p>";
                        $databaseUrl = $_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? getenv('DATABASE_URL');
                        
                        if ($databaseUrl) {
                            $maskedUrl = preg_replace('/:([^:@]+)@/', ':******@', $databaseUrl);
                            echo "<p class='mb-2 ps-4 text-primary-premium'>URL: <code>$maskedUrl</code></p>";
                        } else {
                            echo "<p class='mb-2 ps-4 text-warning-premium'>⚠️ Đang sử dụng DB_HOST mặc định từ file db.php</p>";
                        }

                        // Kiểm tra kết nối
                        $pdo->query("SELECT 1");
                        echo "<p class='mb-2'><i class='bi bi-check-circle-fill text-success-premium me-2'></i> Kết nối Postgres Server thành công.</p>";

                        // Chạy SQL
                        echo "<p class='mb-2 mt-3'><i class='bi bi-hdd-network text-primary-premium me-2'></i> <strong>Bắt đầu khởi tạo các Table...</strong></p>";
                        $sqlFile = 'php/config/init_db.sql';
                        if (!file_exists($sqlFile)) {
                            throw new Exception("File $sqlFile không tồn tại trên hệ thống!");
                        }

                        $sql = file_get_contents($sqlFile);
                        $pdo->exec($sql);
                        
                        echo "<p class='mb-0'><i class='bi bi-check-circle-fill text-success-premium me-2'></i> Khởi tạo Schema và Mock Data thành công!</p>";
                        $isSuccess = true;

                    } catch (PDOException $e) {
                        echo "<hr class='my-3'>";
                        echo "<h5 class='text-danger-premium fw-bold mb-2'><i class='bi bi-x-circle-fill me-2'></i>Lỗi thực thi PDO</h5>";
                        echo "<p class='mb-1'>Mã lỗi: <code>" . $e->getCode() . "</code></p>";
                        echo "<p class='mb-0'>" . $e->getMessage() . "</p>";
                    } catch (Exception $e) {
                        echo "<hr class='my-3'>";
                        echo "<h5 class='text-danger-premium fw-bold mb-2'><i class='bi bi-exclamation-triangle-fill me-2'></i>Lỗi logic hệ thống</h5>";
                        echo "<p class='mb-0'>" . $e->getMessage() . "</p>";
                    }
                    ?>
                </div>

                <div class="text-center mt-5">
                    <?php if ($isSuccess): ?>
                        <div class="p-3 bg-success bg-opacity-10 text-success-premium rounded-3 mb-4 fw-bold">
                            <i class="bi bi-party-popper fs-4 d-block mb-2"></i>
                            Mọi thứ đã sẵn sàng. Bạn có thể sử dụng hệ thống.
                        </div>
                        <a href="index.php" class="btn btn-dark rounded-pill px-5 py-2 fw-medium shadow-sm w-100">
                            Truy cập Trang chủ trang Web <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <p class="text-muted small mt-3">Lưu ý: Mật khẩu Admin mẫu là <code>admin123</code></p>
                    <?php else: ?>
                        <div class="p-3 bg-danger bg-opacity-10 text-danger-premium rounded-3 mb-4 fw-bold">
                            <i class="bi bi-x-octagon fs-4 d-block mb-2"></i>
                            Có lỗi trong quá trình cấu hình. Vui lòng kiểm tra lại DATABASE_URL của bạn.
                        </div>
                        <a href="init-db.php" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-clockwise me-1"></i> Thử lại lần nữa
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
