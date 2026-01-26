<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Config;
use App\Core\Database;
use App\Core\RateLimiter;
use App\Core\Security;
use PDO;

final class AuthController
{
    public function showLogin(): void
    {
        if (!Installer::isInstalled()) {
            redirect('/install');
        }
        view('admin/login', ['csrf' => Security::csrfToken()]);
    }

    public function login(): void
    {
        if (!Installer::isInstalled()) {
            redirect('/install');
        }
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $limit = (int) Config::get('RATE_LIMIT_MAX', 5);
        $window = (int) Config::get('RATE_LIMIT_WINDOW', 60);
        if (!RateLimiter::check('admin-login:' . $ip, $limit, $window)) {
            http_response_code(429);
            exit('Too many requests');
        }
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, password_hash FROM admins WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$admin || !Security::verifyPassword($password, $admin['password_hash'])) {
            http_response_code(401);
            exit('Invalid credentials');
        }
        Security::startSession();
        $_SESSION['admin_id'] = $admin['id'];
        redirect('/admin');
    }

    public function logout(): void
    {
        Security::startSession();
        $_SESSION = [];
        session_destroy();
        redirect('/admin/login');
    }
}
