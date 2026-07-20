<?php
require_once dirname(__DIR__) . '/includes/functions.php';

if (isAdminLoggedIn()) {
    redirect('admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $db = getDB();

    if (!$db) {
        $error = 'Database not connected. Import database/schema.sql and update includes/config.php.';
    } else {
        try {
            $stmt = $db->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_user'] = $admin['username'];
                redirect('admin/index.php');
            }
            $error = 'Invalid username or password.';
        } catch (Exception $e) {
            $error = 'Login failed. Ensure the database is set up.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Admin Login — Mustala</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body">
<div class="login-wrap">
    <form class="login-card" method="post">
        <div class="brand">Must<span>ala</span></div>
        <h1>Admin Login</h1>
        <p>Sign in to manage your portfolio content.</p>
        <?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>
        <div class="form-grid">
            <div>
                <label for="username">Username</label>
                <input id="username" name="username" required autofocus autocomplete="username">
            </div>
            <div>
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>
            <button class="btn" type="submit"><i class="fas fa-right-to-bracket"></i> Sign In</button>
        </div>
        <p class="login-meta">Default: <strong>admin</strong> / <strong>password</strong></p>
        <p class="login-meta"><a href="<?= url('index.php') ?>" style="color:var(--a-crimson);font-weight:600">← Back to site</a></p>
    </form>
</div>
</body>
</html>
