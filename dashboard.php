<?php include 'config/db.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");
$uid = $_SESSION['user_id'];
// চেক ভেরিফিকেশন স্ট্যাটাস
$user = $conn->query("SELECT is_verified FROM users WHERE id=$uid")->fetch_assoc();
$verified = $user['is_verified'];
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
            <?php if ($verified): ?> <span class="badge bg-success">✅ ভেরিফাইড</span>
            <?php else: ?> <span class="badge bg-secondary">⏳ অপুনিপ্টিত</span> <?php endif; ?>
        </h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>আমার আবেদনসমূহ</h5>
                        <ul>
                            <?php
                            $apps = $conn->query("SELECT jobs.title FROM applications JOIN jobs ON applications.job_id=jobs.id WHERE user_id=$uid");
                            while ($a = $apps->fetch_assoc()) echo "<li>{$a['title']}</li>";
                            if ($apps->num_rows == 0) echo "<li>কোনো আবেদন নেই</li>";
                            ?>
                        </ul>
                        <a href="index.php" class="btn btn-primary">নতুন চাকরি দেখুন</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>স্কিল টেস্ট (ভেরিফিকেশন)</h5>
                        <?php if (!$verified): ?>
                            <a href="profile.php" class="btn btn-warning">টেস্ট দিন & ভেরিফাইড হন</a>
                        <?php else: ?>
                            <p class="text-success">আপনি ইতিমধ্যে ভেরিফাইড!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <a href="logout.php" class="btn btn-danger mt-3">লগআউট</a>
    </div>
</body>

</html>
