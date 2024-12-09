<?php
$host = '127.0.0.1';
$dbname = 'suci_id';
$username = 'root';
$password = 'rootpassword';
$port = '8082';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
}
