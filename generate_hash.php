<?php
// Generate password hashes for SQL file
$adminPass = password_hash('admin123', PASSWORD_DEFAULT);
$userPass = password_hash('Test123!', PASSWORD_DEFAULT);

echo "=== PASSWORD HASHES ===\n\n";
echo "Admin (admin123):\n$adminPass\n\n";
echo "User (Test123!):\n$userPass\n\n";
echo "=== COPY THESE INTO init_db.sql ===\n";
?>
