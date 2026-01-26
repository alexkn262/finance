<?php
$siteName = config('APP_NAME', 'Finance');
$seoTitle = $seoTitle ?? $siteName . ' | Finance Education';
$seoDescription = $seoDescription ?? 'Modern finance education, tools, and guides.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($seoTitle) ?></title>
    <meta name="description" content="<?= e($seoDescription) ?>">
    <link rel="stylesheet" href="<?= asset_url('assets/css/style.css') ?>">
    <script defer src="<?= asset_url('assets/js/app.js') ?>"></script>
    <script type="application/ld+json"><?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $siteName,
        'url' => base_url('/'),
    ], JSON_UNESCAPED_SLASHES) ?></script>
</head>
<body class="dark">
    <header class="site-header">
        <div class="container">
            <a class="logo" href="<?= base_url('/') ?>"><?= e($siteName) ?></a>
            <nav>
                <a href="<?= base_url('/start-here') ?>">Start Here</a>
                <a href="<?= base_url('/blog') ?>">Guides</a>
                <a href="<?= base_url('/tools') ?>">Tools</a>
                <a href="<?= base_url('/admin') ?>">Admin</a>
            </nav>
            <div class="search">
                <input id="live-search" type="search" placeholder="Search guides...">
                <div id="search-results" class="search-results"></div>
            </div>
        </div>
    </header>
    <main>
        <?= $content ?? '' ?>
    </main>
    <footer class="site-footer">
        <div class="container">
            <p>© <?= date('Y') ?> <?= e($siteName) ?>. Built for long-term financial authority.</p>
        </div>
    </footer>
</body>
</html>
