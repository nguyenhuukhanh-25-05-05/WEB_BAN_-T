<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "<h1>NHK Mobile Debug Mode</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Checking database configuration...</p>";
$databaseUrl = getenv('DATABASE_URL');
if ($databaseUrl) {
    echo "<p>DATABASE_URL is set.</p>";
} else {
    echo "<p style='color:red;'>DATABASE_URL is NOT set!</p>";
}
echo "<p>Test complete.</p>";
?>
