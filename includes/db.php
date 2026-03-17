// 1. Tìm DATABASE_URL (Cách phổ biến nhất trên Render/Heroku)
$databaseUrl = getenv('DATABASE_URL');
if (!$databaseUrl) $databaseUrl = $_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? null;

if ($databaseUrl) {
    // Nếu có URL, bóc tách thông tin
    $dbParts = parse_url($databaseUrl);
    $host = $dbParts['host'] ?? 'localhost';
    $port = $dbParts['port'] ?? '5432';
    $db   = isset($dbParts['path']) ? ltrim($dbParts['path'], '/') : '';
    $user = $dbParts['user'] ?? '';
    $pass = $dbParts['pass'] ?? '';
} else {
    // 2. Dự phòng các biến lẻ (DB_HOST, DB_USER...)
    $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? 'localhost');
    $port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? '5432');
    $db   = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? 'web_ban_dien_thoai');
    $user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? 'postgres');
    $pass = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? '123456');
}

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    PDO::ATTR_EMULATE_PREPARES   => false,                  
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);

     // TỰ ĐỘNG TẠO BẢNG (Giống Migration trong C#)
     // Kiểm tra xem bảng 'products' đã tồn tại chưa
     $tableCheck = $pdo->query("SELECT 1 FROM information_schema.tables WHERE table_name = 'products'");
     if (!$tableCheck->fetch()) {
         // Nếu chưa có bảng, tự động chạy file init_db.sql
         $sqlFile = __DIR__ . '/../php/config/init_db.sql';
         if (file_exists($sqlFile)) {
             $sql = file_get_contents($sqlFile);
             $pdo->exec($sql);
         }
     }

} catch (\PDOException $e) {
     die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>
