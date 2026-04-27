<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>লগইন / রেজিস্টার</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5" style="max-width:500px">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#login">লগইন</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#register">রেজিস্টার</button></li>
        </ul>
        <div class="tab-content mt-3">

            <!-- লগইন -->
            <div class="tab-pane active" id="login">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="email" name="email" class="form-control mb-2" placeholder="ইমেইল" required>
                    <input type="password" name="password" class="form-control mb-2" placeholder="পাসওয়ার্ড" required>
                    <button type="submit" name="login" class="btn btn-primary w-100">লগইন</button>
                </form>
                <?php
                if (isset($_POST['login'])) {
                    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                        echo "<div class='alert alert-danger mt-2'>Invalid request.</div>";
                    } else {
                        $email = $_POST['email'];
                        $pass  = $_POST['password'];

                        // ✅ Prepared statement
                        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($user = $result->fetch_assoc()) {
                            if (password_verify($pass, $user['password'])) {
                                $_SESSION['user_id']   = $user['id'];
                                $_SESSION['user_name'] = $user['name'];
                                $_SESSION['user_role'] = $user['role'];
                                if ($user['role'] == 'admin') header("Location: admin/dashboard.php");
                                else header("Location: dashboard.php");
                                exit;
                            } else {
                                echo "<div class='alert alert-danger mt-2'>ভুল পাসওয়ার্ড</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger mt-2'>ইমেইল নেই</div>";
                        }
                        $stmt->close();
                    }
                }
                ?>
            </div>

            <!-- রেজিস্টার -->
            <div class="tab-pane" id="register">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="text" name="name" class="form-control mb-2" placeholder="নাম" required>
                    <input type="email" name="email" class="form-control mb-2" placeholder="ইমেইল" required>
                    <input type="password" name="password" class="form-control mb-2" placeholder="পাসওয়ার্ড" required>
                    <button type="submit" name="register" class="btn btn-success w-100">রেজিস্টার</button>
                </form>
                <?php
                if (isset($_POST['register'])) {
                    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                        echo "<div class='alert alert-danger mt-2'>Invalid request.</div>";
                    } else {
                        $name  = trim($_POST['name']);
                        $email = trim($_POST['email']);
                        $hash  = password_hash($_POST['password'], PASSWORD_DEFAULT);

                        // ✅ Prepared statement
                        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                        $stmt->bind_param("sss", $name, $email, $hash);

                        if ($stmt->execute()) {
                            echo "<div class='alert alert-success mt-2'>রেজিস্ট্রেশন সফল! এখন লগইন করুন</div>";
                        } else {
                            echo "<div class='alert alert-danger mt-2'>এই ইমেইল আগে থেকেই নিবন্ধিত।</div>";
                        }
                        $stmt->close();
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
