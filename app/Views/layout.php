<?php
$siteName = config('APP_NAME', 'Finance');
$seoTitle = $seoTitle ?? $siteName . ' | Finance Education';
$seoDescription = $seoDescription ?? 'Modern finance education, tools, and guides.';
$nonce = App\Core\Security::cspNonce();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($seoTitle) ?></title>
    <meta name="description" content="<?= e($seoDescription) ?>">
    <link rel="canonical" href="<?= current_url() ?>">
    <?php if (!empty($ampUrl)): ?>
        <link rel="amphtml" href="<?= e($ampUrl) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= asset_url('assets/css/style.css') ?>">
    <script defer src="<?= asset_url('assets/js/app.js') ?>" nonce="<?= e($nonce) ?>"></script>
    <script type="application/ld+json" nonce="<?= e($nonce) ?>"><?= json_encode([
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
            <div class="footer-grid">
                <div>
                    <h3><?= e($siteName) ?></h3>
                    <p>Modern finance education, tools, and authority resources built for long-term growth.</p>
                </div>
                <div>
                    <h4>Explore</h4>
                    <a href="<?= base_url('/blog') ?>">Guides</a>
                    <a href="<?= base_url('/tools') ?>">Tools</a>
                    <a href="<?= base_url('/start-here') ?>">Start Here</a>
                </div>
                <div>
                    <h4>Company</h4>
                    <a href="<?= base_url('/privacy') ?>">Privacy</a>
                    <a href="<?= base_url('/terms') ?>">Terms</a>
                    <a href="<?= base_url('/contact') ?>">Contact</a>
                </div>
                <div>
                    <h4>Newsletter</h4>
                    <form method="post" action="<?= base_url('/newsletter') ?>" class="footer-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="email" name="email" placeholder="you@example.com" required>
                        <button class="btn" type="submit">Subscribe</button>
                    </form>
                </div>
            </div>
            <p>© <?= date('Y') ?> <?= e($siteName) ?>. Built for long-term financial authority.</p>
        </div>
    </footer>
</body>
</html>
