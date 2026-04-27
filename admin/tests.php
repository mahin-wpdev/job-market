<?php
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

include '../config/db.php';

// ✅ DELETE — prepared statement + exit
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM tests WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: tests.php");
    exit;
}

// ✅ INSERT / UPDATE — prepared statement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_test'])) {
    $id    = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title']);
    $pass  = intval($_POST['passing_score']);

    if ($id) {
        $stmt = $conn->prepare("UPDATE tests SET title = ?, passing_score = ? WHERE id = ?");
        $stmt->bind_param("sii", $title, $pass, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO tests (title, passing_score) VALUES (?, ?)");
        $stmt->bind_param("si", $title, $pass);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: tests.php");
    exit;
}

$tests = $conn->query("SELECT * FROM tests ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>টেস্ট ম্যানেজমেন্ট</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>📝 টেস্ট ক্যাটাগরি</h2>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#testModal" onclick="resetTestForm()">+ নতুন টেস্ট</button>
        <table class="table table-bordered">
            <thead>
                <tr><th>ID</th><th>টেস্ট নাম</th><th>পাসিং স্কোর (%)</th><th>অ্যাকশন</th></tr>
            </thead>
            <tbody>
                <?php while ($t = $tests->fetch_assoc()): ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td><?= htmlspecialchars($t['title']) ?></td>
                        <td><?= $t['passing_score'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#testModal"
                                onclick="editTest(<?= htmlspecialchars(json_encode($t)) ?>)">Edit</button>
                            <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('মুছবেন?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">← ড্যাশবোর্ডে ফিরুন</a>
    </div>

    <div class="modal fade" id="testModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5>টেস্ট তথ্য</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="test_id">
                        <input type="text" name="title" id="test_title" class="form-control mb-2" placeholder="টেস্ট নাম" required>
                        <input type="number" name="passing_score" id="passing_score" class="form-control" placeholder="পাসিং স্কোর (1-100)" value="80" min="1" max="100">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="save_test" class="btn btn-primary">সংরক্ষণ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetTestForm() {
            document.getElementById('test_id').value       = '';
            document.getElementById('test_title').value    = '';
            document.getElementById('passing_score').value = 80;
        }
        function editTest(t) {
            document.getElementById('test_id').value       = t.id;
            document.getElementById('test_title').value    = t.title;
            document.getElementById('passing_score').value = t.passing_score;
        }
    </script>
</body>

</html>
