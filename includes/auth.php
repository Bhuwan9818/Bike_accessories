<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_user']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function login(string $username, string $password): bool {
    $st = db()->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
    $st->execute([$username]);
    $user = $st->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_user'] = $user['username'];
        $_SESSION['admin_name'] = $user['full_name'];
        return true;
    }
    return false;
}

function logout(): void {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

function adminName(): string {
    return $_SESSION['admin_name'] ?? 'Admin';
}
