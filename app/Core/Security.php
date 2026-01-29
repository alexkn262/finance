<?php

declare(strict_types=1);

namespace App\Core;

final class Security
{
    private static ?string $nonce = null;

    public static function initHeaders(): void
    {
        $nonce = self::cspNonce();
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        header('Cross-Origin-Opener-Policy: same-origin');
        header('Cross-Origin-Resource-Policy: same-site');
        header("Content-Security-Policy: default-src 'self'; img-src 'self' https: http: data:; style-src 'self' 'unsafe-inline'; script-src 'self' 'nonce-{$nonce}'; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");
    }

    public static function cspNonce(): string
    {
        if (!self::$nonce) {
            self::$nonce = bin2hex(random_bytes(16));
        }
        return self::$nonce;
    }

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $secure = (bool) Config::get('SESSION_SECURE', false);
        $name = Config::get('SESSION_NAME', 'finance_session');
        $sameSite = Config::get('SESSION_SAMESITE', 'Lax');
        session_name($name);
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => $sameSite,
        ]);
        session_start();
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
    }

    public static function csrfToken(): string
    {
        self::startSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrf(?string $token): bool
    {
        self::startSession();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
    }

    public static function sanitizeHtml(string $html): string
    {
        $allowed = '<p><br><strong><em><ul><ol><li><a><blockquote><code><pre><h2><h3><h4><span><div><img>';
        $clean = strip_tags($html, $allowed);
        $clean = preg_replace('/on\w+\s*=\s*"[^"]*"/i', '', $clean);
        $clean = preg_replace("/javascript:/i", '', $clean);
        $clean = preg_replace_callback('/<img[^>]*>/i', function (array $matches): string {
            $tag = $matches[0];
            if (!preg_match('/src\s*=\s*("|\')([^"\']+)\1/i', $tag, $srcMatch)) {
                return '';
            }
            $src = $srcMatch[2];
            if (!preg_match('#^(https?:)?//#i', $src)) {
                return '';
            }
            $alt = '';
            if (preg_match('/alt\s*=\s*("|\')([^"\']*)\1/i', $tag, $altMatch)) {
                $alt = htmlspecialchars($altMatch[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            }
            return '<img src="' . htmlspecialchars($src, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" alt="' . $alt . '" loading="lazy">';
        }, $clean);
        $clean = $clean ?? '';
        if (trim($clean) === '') {
            return '';
        }
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML(
            '<div>' . $clean . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $wrapper = $doc->getElementsByTagName('div')->item(0);
        if (!$wrapper) {
            return $clean;
        }
        $output = '';
        foreach ($wrapper->childNodes as $child) {
            $output .= $doc->saveHTML($child);
        }
        return $output;
    }

    public static function isValidEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        if (strlen($email) > 254) {
            return false;
        }
        $domain = substr(strrchr($email, '@'), 1) ?: '';
        return $domain !== '' && preg_match('/\\./', $domain) === 1;
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
