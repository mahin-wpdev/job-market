<?php
                            // প্রতিটি admin/*.php ফাইলের শুরুতে
                            if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
                                header("Location: index.php");
                                exit;
                            }
                            
include '../config/db.php';
if (!isset($_SESSION['admin_id'])) header("Location: index.php");

// ডিলিট
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM questions WHERE id = $id");
    header("Location: questions.php");
}

// সেভ (যোগ বা আপডেট)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_question'])) {
    $id = $_POST['id'] ?? 0;
    $test_id = $_POST['test_id'];
    $text = $conn->real_escape_string($_POST['question_text']);
    $opt_a = $conn->real_escape_string($_POST['opt_a']);
    $opt_b = $conn->real_escape_string($_POST['opt_b']);
    $opt_c = $conn->real_escape_string($_POST['opt_c']);
    $opt_d = $conn->real_escape_string($_POST['opt_d']);
    $correct = $_POST['correct_opt'];

    if ($id) {
        $conn->query("UPDATE questions SET
            test_id = $test_id,
            question_text = '$text',
            option_a = '$opt_a',
            option_b = '$opt_b',
            option_c = '$opt_c',
            option_d = '$opt_d',
            correct_option = '$correct'
            WHERE id = $id");
    } else {
        $conn->query("INSERT INTO questions (test_id, question_text, option_a, option_b, option_c, option_d, correct_option)
            VALUES ($test_id, '$text', '$opt_a', '$opt_b', '$opt_c', '$opt_d', '$correct')");
    }
    header("Location: questions.php");
}

// সব প্রশ্ন টেস্টের নামসহ
$questions = $conn->query("SELECT q.*, t.title as test_title FROM questions q JOIN tests t ON q.test_id = t.id ORDER BY q.id DESC");

// টেস্টের লিস্ট (ড্রপডাউনের জন্য)
$tests = $conn->query("SELECT id, title FROM tests");
?>

<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>প্রশ্ন ব্যবস্থাপনা</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>❓ প্রশ্ন ব্যবস্থাপনা</h2>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#qModal" onclick="resetQForm()">+ নতুন প্রশ্ন</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>টেস্ট</th>
                    <th>প্রশ্ন</th>
                    <th>সঠিক উত্তর</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($q = $questions->fetch_assoc()): ?>
                    <tr>
                        <td><?= $q['id'] ?></td>
                        <td><?= htmlspecialchars($q['test_title']) ?></td>
                        <td><?= htmlspecialchars(substr($q['question_text'], 0, 80)) ?></td>
                        <td><?= strtoupper($q['correct_option']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#qModal" onclick="editQuestion(<?= htmlspecialchars(json_encode($q)) ?>)">Edit</button>
                            <a href="?delete=<?= $q['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('মুছবেন?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal ফর্ম -->
    <div class="modal fade" id="qModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5>প্রশ্ন তথ্য</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="q_id">
                        <select name="test_id" id="test_id" class="form-select mb-2" required>
                            <?php $tests->data_seek(0);
                            while ($t = $tests->fetch_assoc()): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['title']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <input type="text" name="question_text" id="question_text" class="form-control mb-2" placeholder="প্রশ্ন লিখুন" required>
                        <div class="row">
                            <div class="col-md-6"><input type="text" name="opt_a" id="opt_a" class="form-control mb-1" placeholder="অপশন A"></div>
                            <div class="col-md-6"><input type="text" name="opt_b" id="opt_b" class="form-control mb-1" placeholder="অপশন B"></div>
                            <div class="col-md-6"><input type="text" name="opt_c" id="opt_c" class="form-control mb-1" placeholder="অপশন C"></div>
                            <div class="col-md-6"><input type="text" name="opt_d" id="opt_d" class="form-control mb-1" placeholder="অপশন D"></div>
                        </div>
                        <select name="correct_opt" id="correct_opt" class="form-select mt-2">
                            <option value="a">A</option>
                            <option value="b">B</option>
                            <option value="c">C</option>
                            <option value="d">D</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="save_question" class="btn btn-primary">সংরক্ষণ করুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetQForm() {
            document.getElementById('q_id').value = '';
            document.getElementById('question_text').value = '';
            document.getElementById('opt_a').value = '';
            document.getElementById('opt_b').value = '';
            document.getElementById('opt_c').value = '';
            document.getElementById('opt_d').value = '';
            document.getElementById('correct_opt').value = 'a';
        }

        function editQuestion(q) {
            document.getElementById('q_id').value = q.id;
            document.getElementById('test_id').value = q.test_id;
            document.getElementById('question_text').value = q.question_text;
            document.getElementById('opt_a').value = q.option_a;
            document.getElementById('opt_b').value = q.option_b;
            document.getElementById('opt_c').value = q.option_c;
            document.getElementById('opt_d').value = q.option_d;
            document.getElementById('correct_opt').value = q.correct_option;
        }
    </script>
</body>

</html>
