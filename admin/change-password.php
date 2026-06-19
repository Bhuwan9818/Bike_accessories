<?php
$pageTitle = 'Change Password';
$activeNav = 'password';
$adminDepth = 1;
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/_layout.php';

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current  = $_POST['current_password'] ?? '';
    $new      = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Verify current password
    $st = db()->prepare("SELECT password FROM admin_users WHERE id=?");
    $st->execute([$_SESSION['admin_id']]);
    $row = $st->fetch();

    if (!$row || !password_verify($current, $row['password'])) {
        $errors[] = 'Current password is incorrect.';
    } elseif (strlen($new) < 6) {
        $errors[] = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $errors[] = 'New passwords do not match.';
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        db()->prepare("UPDATE admin_users SET password=? WHERE id=?")->execute([$hash, $_SESSION['admin_id']]);
        $success = 'Password changed successfully.';
    }
}
?>

<?php if ($success): ?>
<div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($errors): ?>
<div class="alert alert-danger">
  <?php foreach ($errors as $e): ?><div>❌ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<div style="max-width:480px">
  <form method="POST" class="admin-form">
    <div class="form-card">
      <div class="form-card-title">Change Password</div>
      <div class="form-group">
        <label>Current Password</label>
        <input type="password" name="current_password" required autofocus>
      </div>
      <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" required minlength="6">
        <div class="form-hint">Minimum 6 characters</div>
      </div>
      <div class="form-group">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required>
      </div>
      <button type="submit" class="btn btn-red">🔑 Update Password</button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
