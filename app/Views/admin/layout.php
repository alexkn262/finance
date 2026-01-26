<?php $siteName = config('APP_NAME', 'Finance'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin Dashboard') ?></title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/admin.css') ?>">
</head>
<body class="admin">
    <aside class="sidebar">
        <h2><?= e($siteName) ?> Admin</h2>
        <nav>
            <a href="<?= base_url('/admin') ?>">Dashboard</a>
            <a href="<?= base_url('/admin/articles') ?>">Articles</a>
            <a href="<?= base_url('/admin/articles/ai') ?>">AI Studio</a>
            <a href="<?= base_url('/admin/categories') ?>">Categories</a>
            <a href="<?= base_url('/admin/tags') ?>">Tags</a>
            <a href="<?= base_url('/admin/comments') ?>">Comments</a>
            <a href="<?= base_url('/admin/analytics') ?>">Analytics</a>
            <a href="<?= base_url('/admin/settings') ?>">Settings</a>
        </nav>
        <form method="post" action="<?= base_url('/admin/logout') ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <button type="submit">Log out</button>
        </form>
    </aside>
    <main class="admin-main">
        <?= $content ?? '' ?>
    </main>
</body>
</html>
