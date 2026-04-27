<?php
$host   = 'localhost';
$user   = 'skillverify_user'; // root ব্যবহার করবেন না
$pass   = 'YOUR_STRONG_PASSWORD'; // শক্তিশালী পাসওয়ার্ড দিন
$dbname = 'skillverify_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// CSRF token generate করুন (একবার প্রতি session)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
