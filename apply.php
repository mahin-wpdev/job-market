<?php
include 'config/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

if ($job_id <= 0) {
    die("Invalid job ID.");
}

// চেক করা যে এই জব টি আসলেই আছে কিনা
$jobCheck = $conn->query("SELECT id FROM jobs WHERE id = $job_id");
if ($jobCheck->num_rows == 0) {
    die("Job not found.");
}

// আগে আবেদন করা হয়েছে কিনা
$existing = $conn->query("SELECT id FROM applications WHERE user_id = $user_id AND job_id = $job_id");
if ($existing->num_rows > 0) {
    echo "<script>alert('আপনি ইতিমধ্যে এই চাকরিতে আবেদন করেছেন।'); window.location='index.php';</script>";
    exit;
}

// আবেদন সংরক্ষণ
$stmt = $conn->prepare("INSERT INTO applications (user_id, job_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $job_id);
if ($stmt->execute()) {
    echo "<script>alert('আবেদন সফল হয়েছে!'); window.location='dashboard.php';</script>";
} else {
    echo "<script>alert('আবেদন করতে সমস্যা হয়েছে, পরে আবার চেষ্টা করুন।'); window.location='index.php';</script>";
}
$stmt->close();
$conn->close();
