<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>SkillVerify Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">✅ SkillVerify</a>
            <div class="ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-light">ড্যাশবোর্ড</a>
                    <a href="logout.php" class="btn btn-outline-light">লগআউট</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-light">লগইন / রেজিস্টার</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>🔥 সক্রিয় চাকরির সার্কুলার</h2>
        <div class="row">
            <?php
            $result = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC");
            while ($job = $result->fetch_assoc()):
            ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($job['title']) ?></h5>
                            <p class="card-text">
                                <strong><?= htmlspecialchars($job['company']) ?></strong><br>
                                📍 <?= htmlspecialchars($job['location']) ?><br>
                                💰 <?= htmlspecialchars($job['salary']) ?>
                            </p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="apply.php?job_id=<?= $job['id'] ?>" class="btn btn-primary">আবেদন করুন</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-secondary">আবেদন করতে লগইন করুন</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>

</html>
