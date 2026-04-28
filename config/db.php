<?php
$host   = '49.12.82.48';
$user   = 'jtexjhen_pay'; // root ব্যবহার করবেন না
$pass   = 'Ph2x7k7PP.9.Uy'; // শক্তিশালী পাসওয়ার্ড দিন
$dbname = 'jtexjhen_pay';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// CSRF token generate করুন (একবার প্রতি session)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
