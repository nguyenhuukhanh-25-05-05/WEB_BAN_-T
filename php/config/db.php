<?php
$host = 'localhost';
$port = '5432';
$db   = 'nhkmobile_db';
$user = 'postgres';
$pass = 'your_password'; 
$charset = 'utf8';

$dsn = "pgsql:host=$host;port=$port;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     header('Content-Type: application/json');
     echo json_encode(['error' => 'Kết nối CSDL thất bại: ' . $e->getMessage()]);
     exit;
}
?>
