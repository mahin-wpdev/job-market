<?php
                // প্রতিটি admin/*.php ফাইলের শুরুতে
                if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
                    header("Location: index.php");
                    exit;
                }

include '../config/db.php';

// যদি ইতিমধ্যে লগইন করা থাকে তাহলে ড্যাশবোর্ডে পাঠিয়ে দিন
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email' AND role = 'admin'");
    if ($result && $result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "পাসওয়ার্ড ভুল!";
        }
    } else {
        $error = "অ্যাডমিন ইউজার পাওয়া যায়নি!";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <title>অ্যাডমিন লগইন - স্কিলভেরিফাই</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: #2a5298;
            color: white;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border-radius: 1rem 1rem 0 0 !important;
        }

        .btn-primary {
            background: #2a5298;
            border: none;
        }

        .btn-primary:hover {
            background: #1e3c72;
        }
    </style>
</head>

<body>
    <div class="container" style="max-width: 450px;">
        <div class="card">
            <div class="card-header">
                🔐 অ্যাডমিন প্যানেল লগইন
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">ইমেইল ঠিকানা</label>
                        <input type="email" name="email" class="form-control" placeholder="admin@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">পাসওয়ার্ড</label>
                        <input type="password" name="password" class="form-control" placeholder="পাসওয়ার্ড দিন" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">লগইন করুন</button>
                </form>
                <div class="mt-3 text-center text-muted">
                    <small>ডিফল্ট: admin@example.com / admin123</small>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
