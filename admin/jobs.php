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

// ডিলিট
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM jobs WHERE id = $id");
    header("Location: jobs.php");
    exit;
}

// সেভ (যোগ বা আপডেট)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_job'])) {
    $id = $_POST['id'] ?? 0;
    $title = $conn->real_escape_string($_POST['title']);
    $company = $conn->real_escape_string($_POST['company']);
    $location = $conn->real_escape_string($_POST['location']);
    $salary = $conn->real_escape_string($_POST['salary']);
    $description = $conn->real_escape_string($_POST['description']);

    if ($id) {
        $conn->query("UPDATE jobs SET
            title = '$title',
            company = '$company',
            location = '$location',
            salary = '$salary',
            description = '$description'
            WHERE id = $id");
    } else {
        $conn->query("INSERT INTO jobs (title, company, location, salary, description)
            VALUES ('$title', '$company', '$location', '$salary', '$description')");
    }
    header("Location: jobs.php");
    exit;
}

// সব জব লোড
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
                    <th>ID</th>
                    <th>শিরোনাম</th>
                    <th>কোম্পানি</th>
                    <th>স্থান</th>
                    <th>বেতন</th>
                    <th>অ্যাকশন</th>
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

    <!-- Modal ফর্ম (যোগ/এডিট) -->
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
            document.getElementById('job_id').value = '';
            document.getElementById('title').value = '';
            document.getElementById('company').value = '';
            document.getElementById('location').value = '';
            document.getElementById('salary').value = '';
            document.getElementById('description').value = '';
        }

        function editJob(job) {
            document.getElementById('job_id').value = job.id;
            document.getElementById('title').value = job.title;
            document.getElementById('company').value = job.company;
            document.getElementById('location').value = job.location;
            document.getElementById('salary').value = job.salary;
            document.getElementById('description').value = job.description;
        }
    </script>
</body>

</html>
