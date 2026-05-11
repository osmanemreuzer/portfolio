<?php
session_start();
session_regenerate_id(true);

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

require_once '../db/connection.php';

$error = '';

// Handle "Remember Me" cookie auto-login
if (!isset($_SESSION['admin_logged_in']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $stmt = $conn->prepare('SELECT id, username FROM admin_users WHERE remember_token = ? LIMIT 1');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id']       = $row['id'];
        $_SESSION['admin_user']     = $row['username'];
        header('Location: dashboard.php');
        exit;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $user['id'];
            $_SESSION['admin_user']      = $user['username'];

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 3600), '/', '', false, true);
                $stmt2 = $conn->prepare('UPDATE admin_users SET remember_token = ? WHERE id = ?');
                $stmt2->bind_param('si', $token, $user['id']);
                $stmt2->execute();
                $stmt2->close();
            }

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login | Portfolio</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/admin.css" />
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</head>
<body>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-header">
        <a href="../index.html" class="nav-logo">OEÜ<span>.</span></a>
        <h1>Admin Login</h1>
        <p>Sign in to manage your portfolio</p>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="" class="auth-form" novalidate>
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 placeholder="admin" autocomplete="username" />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required
                 placeholder="••••••••" autocomplete="current-password" />
        </div>
        <div class="form-check">
          <input type="checkbox" id="remember" name="remember" />
          <label for="remember">Remember me for 30 days</label>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Sign In</button>
      </form>

      <div class="auth-footer">
        <a href="../index.html">← Back to Portfolio</a>
      </div>
    </div>
  </div>
  <script src="../js/main.js" defer></script>
</body>
</html>
