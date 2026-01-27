<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Config;
use App\Core\Installer;
use App\Core\Security;

final class InstallerController
{
    public function wizard(): void
    {
        if (Installer::isInstalled()) {
            redirect('/admin/login');
        }
        $requirements = Installer::requirements();
        view('admin/installer', [
            'requirements' => $requirements,
            'csrf' => Security::csrfToken(),
        ]);
    }

    public function complete(): void
    {
        if (Installer::isInstalled()) {
            redirect('/admin/login');
        }
        if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
        $required = ['site_name', 'site_url', 'admin_name', 'admin_email', 'admin_password'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                http_response_code(422);
                exit('Missing field: ' . $field);
            }
        }
        Installer::runMigrations();
        Installer::setupAdmin($_POST['admin_name'], $_POST['admin_email'], $_POST['admin_password']);
        Installer::saveSettings([
            'site_name' => $_POST['site_name'],
            'site_url' => $_POST['site_url'],
            'seo_title' => $_POST['seo_title'] ?? 'Finance Education Platform',
            'seo_description' => $_POST['seo_description'] ?? 'Premium finance education, tools, and guides.',
            'newsletter_status' => 'enabled',
        ]);
        Installer::seedDemoContent();
        Config::set('APP_NAME', $_POST['site_name']);
        Config::set('APP_URL', $_POST['site_url']);
        Installer::lock();
        redirect('/admin/login');
    }
}
