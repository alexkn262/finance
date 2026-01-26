<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Finance Platform</title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/admin.css') ?>">
</head>
<body class="admin">
    <main class="installer">
        <h1>Install Finance Platform</h1>
        <section class="requirements">
            <h2>System requirements</h2>
            <ul>
                <li class="<?= $requirements['php_version'] ? 'ok' : 'fail' ?>">PHP 8.2+</li>
                <li class="<?= $requirements['pdo_sqlite'] ? 'ok' : 'fail' ?>">PDO SQLite enabled</li>
                <li class="<?= $requirements['storage_writable'] ? 'ok' : 'fail' ?>">Storage writable</li>
            </ul>
        </section>
        <form method="post" action="<?= base_url('/install') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
            <label>Site name <input type="text" name="site_name" required></label>
            <label>Base URL <input type="url" name="site_url" required></label>
            <label>SEO title <input type="text" name="seo_title"></label>
            <label>SEO description <input type="text" name="seo_description"></label>
            <label>Admin name <input type="text" name="admin_name" required></label>
            <label>Admin email <input type="email" name="admin_email" required></label>
            <label>Admin password <input type="password" name="admin_password" required></label>
            <button class="btn" type="submit">Complete install</button>
        </form>
    </main>
</body>
</html>
