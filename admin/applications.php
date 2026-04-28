<?php
                // প্রতিটি admin/*.php ফাইলের শুরুতে
                if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
                    header("Location: index.php");
                    exit;
                }
                
include '../config/db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// সব আবেদন দেখাবে, সর্বশেষ প্রথমে
$applications = $conn->query("
    SELECT a.id, u.name as user_name, u.email as user_email, j.title as job_title,
           j.company, a.applied_at
    FROM applications a
    JOIN users u ON a.user_id = u.id
    JOIN jobs j ON a.job_id = j.id
    ORDER BY a.applied_at DESC
");
?>

<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>আবেদন সমূহ - অ্যাডমিন</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th {
            background-color: #2a5298;
            color: white;
        }

        .badge-date {
            background: #e9ecef;
            color: #2c3e50;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>📬 চাকরির আবেদনপত্র</h2>
            <a href="dashboard.php" class="btn btn-secondary">← ড্যাশবোর্ড</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <?php if ($applications && $applications->num_rows > 0): ?>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>প্রার্থীর নাম</th>
                                <th>ইমেইল</th>
                                <th>চাকরির শিরোনাম</th>
                                <th>কোম্পানি</th>
                                <th>আবেদনের তারিখ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($app = $applications->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $app['id'] ?></td>
                                    <td><?= htmlspecialchars($app['user_name']) ?></td>
                                    <td><?= htmlspecialchars($app['user_email']) ?></td>
                                    <td><?= htmlspecialchars($app['job_title']) ?></td>
                                    <td><?= htmlspecialchars($app['company']) ?></td>
                                    <td><?= date('d-m-Y h:i A', strtotime($app['applied_at'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">কোনো আবেদন এখনও করা হয়নি।</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
