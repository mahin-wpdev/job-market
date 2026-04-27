<?php
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

include '../config/db.php';

// ✅ DELETE — prepared statement
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: jobs.php");
    exit;
}

// ✅ INSERT / UPDATE — prepared statement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_job'])) {
    $id          = intval($_POST['id'] ?? 0);
    $title       = trim($_POST['title']);
    $company     = trim($_POST['company']);
    $location    = trim($_POST['location']);
    $salary      = trim($_POST['salary']);
    $description = trim($_POST['description']);

    if ($id) {
        $stmt = $conn->prepare("UPDATE jobs SET title=?, company=?, location=?, salary=?, description=? WHERE id=?");
        $stmt->bind_param("sssssi", $title, $company, $location, $salary, $description, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO jobs (title, company, location, salary, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $company, $location, $salary, $description);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: jobs.php");
    exit;
}

$jobs = $conn->query("SELECT * FROM jobs ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>জব ম্যানেজমেন্ট - অ্যাডমিন</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>📋 জব লিস্টিং ম্যানেজ</h2>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#jobModal" onclick="resetForm()">+ নতুন জব যোগ করুন</button>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th><th>শিরোনাম</th><th>কোম্পানি</th><th>স্থান</th><th>বেতন</th><th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($job = $jobs->fetch_assoc()): ?>
                    <tr>
                        <td><?= $job['id'] ?></td>
                        <td><?= htmlspecialchars($job['title']) ?></td>
                        <td><?= htmlspecialchars($job['company']) ?></td>
                        <td><?= htmlspecialchars($job['location']) ?></td>
                        <td><?= htmlspecialchars($job['salary']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#jobModal"
                                onclick="editJob(<?= htmlspecialchars(json_encode($job)) ?>)">Edit</button>
                            <a href="?delete=<?= $job['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('মুছে ফেলবেন?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">← ড্যাশবোর্ডে ফিরুন</a>
    </div>

    <div class="modal fade" id="jobModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">জব তথ্য</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="job_id">
                        <input type="text" name="title" id="title" class="form-control mb-2" placeholder="শিরোনাম" required>
                        <input type="text" name="company" id="company" class="form-control mb-2" placeholder="কোম্পানির নাম" required>
                        <input type="text" name="location" id="location" class="form-control mb-2" placeholder="ঠিকানা">
                        <input type="text" name="salary" id="salary" class="form-control mb-2" placeholder="বেতন (যেমন: ৫০,০০০ - ৭৫,০০০)">
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="জব বিস্তারিত (অপশনাল)"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="save_job" class="btn btn-primary">সংরক্ষণ করুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            ['job_id','title','company','location','salary','description'].forEach(id => {
                document.getElementById(id).value = '';
            });
        }
        function editJob(job) {
            document.getElementById('job_id').value      = job.id;
            document.getElementById('title').value       = job.title;
            document.getElementById('company').value     = job.company;
            document.getElementById('location').value    = job.location;
            document.getElementById('salary').value      = job.salary;
            document.getElementById('description').value = job.description;
        }
    </script>
</body>

</html>
