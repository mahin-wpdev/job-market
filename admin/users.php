<?php
                // প্রতিটি admin/*.php ফাইলের শুরুতে
                if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
                    header("Location: index.php");
                    exit;
                }
                
include '../config/db.php';
if (!isset($_SESSION['admin_id'])) header("Location: index.php");

$users = $conn->query("SELECT id, name, email, is_verified, created_at FROM users WHERE role = 'user' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="bn">

<head>
    <title>ইউজার লিস্ট</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>👥 নিবন্ধিত ইউজার</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>নাম</th>
                    <th>ইমেইল</th>
                    <th>ভেরিফাইড?</th>
                    <th>যোগদানের তারিখ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= $u['is_verified'] ? '✅ হ্যাঁ' : '❌ না' ?></td>
                        <td><?= $u['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">পেছনে</a>
    </div>
</body>

</html>
