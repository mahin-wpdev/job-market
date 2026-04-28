<?php
// প্রতিটি admin/*.php ফাইলের শুরুতে
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

include '../config/db.php';
if (!isset($_SESSION['admin_id'])) header("Location: index.php");
?>
<!DOCTYPE html>
<html>

<head>
    <title>অ্যাডমিন প্যানেল</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>অ্যাডমিন ড্যাশবোর্ড</h2>
        <div class="row">
            <div class="col-md-3"><a href="jobs.php" class="btn btn-primary w-100 mb-2">📋 জব ম্যানেজ</a></div>
            <div class="col-md-3"><a href="tests.php" class="btn btn-success w-100 mb-2">📝 টেস্ট ম্যানেজ</a></div>
            <div class="col-md-3"><a href="users.php" class="btn btn-info w-100 mb-2">👥 ইউজার লিস্ট</a></div>
            <div class="col-md-3"><a href="applications.php" class="btn btn-warning w-100 mb-2">📬 আবেদন</a></div>
        </div>
        <a href="logout.php" class="btn btn-danger">লগআউট</a>
    </div>
</body>

</html>
