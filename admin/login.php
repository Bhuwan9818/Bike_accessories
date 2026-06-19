<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/admin/');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($user && $pass) {
        if (login($user, $pass)) {
            header('Location: ' . BASE_URL . '/admin/');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Login – Bike Accessories India</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;600;700&family=Barlow:wght@300;400;500;600;700;900&family=Barlow+Condensed:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css">
<style>
body{background:var(--black);display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
.login-wrap{width:100%;max-width:420px;padding:1rem;}
.login-box{background:var(--dark);border:1px solid var(--border);border-radius:12px;padding:2.5rem 2rem;}
.login-logo{text-align:center;margin-bottom:2rem;}
.login-logo-icon{font-size:48px;display:block;margin-bottom:.5rem;}
.login-logo-title{font-family:'Barlow Condensed',sans-serif;font-size:26px;font-weight:800;color:var(--chrome-light);letter-spacing:1px;}
.login-logo-sub{font-size:12px;color:var(--text-muted);letter-spacing:2px;text-transform:uppercase;}
.login-title{font-family:'Barlow Condensed',sans-serif;font-size:22px;font-weight:800;color:var(--chrome-light);margin-bottom:1.5rem;text-align:center;}
.form-group{margin-bottom:1.2rem;}
.form-group label{display:block;font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:.5rem;}
.form-group input{width:100%;background:var(--black);border:1px solid var(--border);color:var(--chrome-light);padding:.75rem 1rem;border-radius:8px;font-size:15px;font-family:'Barlow',sans-serif;transition:border-color .2s;box-sizing:border-box;}
.form-group input:focus{outline:none;border-color:var(--red);}
.login-btn{width:100%;background:var(--red);color:#fff;border:none;padding:.85rem 1rem;border-radius:8px;font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;}
.login-btn:hover{background:#c0392b;}
.login-error{background:rgba(220,38,38,.1);border:1px solid rgba(220,38,38,.3);color:#f87171;padding:.75rem 1rem;border-radius:8px;font-size:13px;margin-bottom:1.2rem;text-align:center;}
.login-hint{text-align:center;font-size:12px;color:var(--text-dim);margin-top:1.5rem;}
.back-link{display:block;text-align:center;margin-top:1rem;color:var(--text-muted);font-size:13px;text-decoration:none;}
.back-link:hover{color:var(--red);}
</style>
</head>
<body>
<div class="login-wrap">
  <div class="login-box">
    <div class="login-logo">
      <span class="login-logo-icon">🏍️</span>
      <div class="login-logo-title">BIKE ACCESSORIES</div>
      <div class="login-logo-sub">Admin Panel</div>
    </div>
    <div class="login-title">SIGN IN</div>
    <?php if ($error): ?>
    <div class="login-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autofocus required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>
      </div>
      <button type="submit" class="login-btn">Login →</button>
    </form>
    <div class="login-hint">Default: admin / Admin@123</div>
  </div>
  <a href="../index.php" class="back-link">← Back to Store</a>
</div>
</body>
</html>
