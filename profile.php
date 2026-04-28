<?php include 'config/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit; // ✅ exit যোগ করা হয়েছে
}
$uid = $_SESSION['user_id'];

// ✅ test_id সবসময় GET থেকে নেওয়া হচ্ছে (POST এর সময়ও)
$test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;
if ($test_id <= 0) {
    die("কোনো টেস্ট নির্বাচন করা হয়নি।");
}

// টেস্ট জমা দেওয়া হলে
if (isset($_POST['submit_test'])) {
    $score = 0;
    $total = 0;

    $stmt = $conn->prepare("SELECT * FROM questions WHERE test_id = ?");
    $stmt->bind_param("i", $test_id);
    $stmt->execute();
    $questions_result = $stmt->get_result();

    while ($q = $questions_result->fetch_assoc()) {
        $total++;
        $ans = $_POST['q_' . $q['id']] ?? '';
        if ($ans == $q['correct_option']) $score++;
    }
    $stmt->close();

    if ($total === 0) {
        die("এই টেস্টে কোনো প্রশ্ন নেই।");
    }

    $percentage = round(($score / $total) * 100, 2);
    $passed = ($percentage >= 80) ? 1 : 0;

    // ✅ Prepared statement — SQL injection নেই
    $stmt = $conn->prepare("INSERT INTO user_test_results (user_id, test_id, score, passed) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iidi", $uid, $test_id, $percentage, $passed);
    $stmt->execute();
    $stmt->close();

    if ($passed) {
        // ✅ Prepared statement
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('অভিনন্দন! আপনি ভেরিফাইড হয়েছেন।'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('স্কোর {$percentage}%। কমপক্ষে 80% প্রয়োজন। আবার চেষ্টা করুন।');</script>";
    }
    exit;
}

// টেস্টের প্রশ্ন দেখানো
$stmt = $conn->prepare("SELECT * FROM questions WHERE test_id = ?");
$stmt->bind_param("i", $test_id);
$stmt->execute();
$questions = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>

<head>
    <title>স্কিল টেস্ট</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>স্কিল ভেরিফিকেশন টেস্ট</h2>

        <!-- ✅ form action-এ test_id পাঠানো হচ্ছে যাতে POST এর সময়ও GET parameter থাকে -->
        <form method="POST" action="profile.php?test_id=<?= $test_id ?>">
            <?php if ($questions->num_rows === 0): ?>
                <div class="alert alert-warning">এই টেস্টে কোনো প্রশ্ন নেই।</div>
            <?php else: ?>
                <?php while ($q = $questions->fetch_assoc()): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6><?= htmlspecialchars($q['question_text']) ?></h6>
                            <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio"
                                        name="q_<?= $q['id'] ?>"
                                        value="<?= $opt ?>"
                                        id="q<?= $q['id'] . $opt ?>">
                                    <label for="q<?= $q['id'] . $opt ?>">
                                        <?= htmlspecialchars($q['option_' . $opt]) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
                <button type="submit" name="submit_test" class="btn btn-primary">সাবমিট করুন</button>
            <?php endif; ?>
        </form>

        <a href="dashboard.php" class="btn btn-secondary mt-2">ড্যাশবোর্ডে ফিরুন</a>
    </div>
</body>

</html>
