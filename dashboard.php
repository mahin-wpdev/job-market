<?php
include 'config/db.php';

// ✅ exit যোগ করা হয়েছে
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

// ✅ Prepared statement
$stmt = $conn->prepare("SELECT is_verified FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user     = $stmt->get_result()->fetch_assoc();
$verified = $user['is_verified'];
$stmt->close();

// আবেদনের তালিকা — prepared statement
$stmt = $conn->prepare("SELECT jobs.title FROM applications JOIN jobs ON applications.job_id = jobs.id WHERE applications.user_id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$apps = $stmt->get_result();
$stmt->close();

// available tests দেখানোর জন্য
$tests = $conn->query("SELECT id, title FROM tests ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>আমার ড্যাশবোর্ড</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>স্বাগতম, <?= htmlspecialchars($_SESSION['user_name']) ?>
            <?php if ($verified): ?>
                <span class="badge bg-success">✅ ভেরিফাইড</span>
            <?php else: ?>
                <span class="badge bg-secondary">⏳ অযাচাইকৃত</span>
            <?php endif; ?>
        </h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>আমার আবেদনসমূহ</h5>
                        <ul>
                            <?php if ($apps->num_rows == 0): ?>
                                <li>কোনো আবেদন নেই</li>
                            <?php else: ?>
                                <?php while ($a = $apps->fetch_assoc()): ?>
                                    <li><?= htmlspecialchars($a['title']) ?></li>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </ul>
                        <a href="index.php" class="btn btn-primary">নতুন চাকরি দেখুন</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>স্কিল টেস্ট (ভেরিফিকেশন)</h5>
                        <?php if ($verified): ?>
                            <p class="text-success">আপনি ইতিমধ্যে ভেরিফাইড!</p>
                        <?php else: ?>
                            <?php if ($tests && $tests->num_rows > 0): ?>
                                <?php while ($t = $tests->fetch_assoc()): ?>
                                    <!-- ✅ test_id সহ link -->
                                    <a href="profile.php?test_id=<?= $t['id'] ?>" class="btn btn-warning mb-1">
                                        📝 <?= htmlspecialchars($t['title']) ?>
                                    </a><br>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-muted">এখনো কোনো টেস্ট তৈরি হয়নি।</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <a href="logout.php" class="btn btn-danger mt-3">লগআউট</a>
    </div>
</body>

</html>
