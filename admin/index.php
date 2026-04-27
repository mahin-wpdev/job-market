<?php
include '../config/db.php';

// ✅ login page-এ role check রাখলে infinite loop হয়, তাই এখানে নেই
// যদি ইতিমধ্যে লগইন করা থাকে তাহলে ড্যাশবোর্ডে পাঠিয়ে দিন
if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin') {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // ✅ CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request.";
    } else {
        $email    = $_POST['email'];
        $password = $_POST['password'];

        // ✅ Prepared statement — SQL injection নেই
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id']   = $admin['id'];
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
        $stmt->close();
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
        .card { border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .card-header {
            background: #2a5298; color: white; text-align: center;
            font-size: 1.5rem; font-weight: bold;
            border-radius: 1rem 1rem 0 0 !important;
        }
        .btn-primary { background: #2a5298; border: none; }
        .btn-primary:hover { background: #1e3c72; }
    </style>
</head>

<body>
    <div class="container" style="max-width: 450px;">
        <div class="card">
            <div class="card-header">🔐 অ্যাডমিন প্যানেল লগইন</div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
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
            </div>
        </div>
    </div>
</body>

</html>
