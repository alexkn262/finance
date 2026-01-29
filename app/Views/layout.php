<?php
$siteName = config('APP_NAME', 'Finance');
$defaultTitle = config('seo_title', $siteName . ' | Finance Education');
$defaultDescription = config('seo_description', 'Modern finance education, tools, and guides.');
$defaultImage = config('seo_image', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1400&q=80');
$seoTitle = $seoTitle ?? config('PAGE_SEO_TITLE') ?? $defaultTitle;
$seoDescription = $seoDescription ?? config('PAGE_SEO_DESCRIPTION') ?? $defaultDescription;
$seoImage = $seoImage ?? config('PAGE_SEO_IMAGE');
$seoImage = ($seoImage === null || $seoImage === '') ? $defaultImage : $seoImage;
$seoType = $seoType ?? config('PAGE_SEO_TYPE') ?? 'website';
$schemaType = $schemaType ?? config('PAGE_SCHEMA_TYPE') ?? 'WebPage';
$schemaData = $schemaData ?? config('PAGE_SCHEMA_DATA');
$breadcrumbs = $breadcrumbs ?? config('PAGE_BREADCRUMBS', []);
$seoPublished = $seoPublished ?? config('PAGE_SEO_PUBLISHED');
$seoModified = $seoModified ?? config('PAGE_SEO_MODIFIED');
$seoSection = $seoSection ?? config('PAGE_SEO_SECTION');
$seoAuthor = $seoAuthor ?? config('PAGE_SEO_AUTHOR', 'Finance Editorial Team');
$seoUrl = $seoUrl ?? current_url();
$nonce = App\Core\Security::cspNonce();

$schemaBlocks = [];
$schemaBlocks[] = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => $siteName,
    'url' => base_url('/'),
    'description' => $defaultDescription,
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => base_url('/search?q={search_term_string}'),
        'query-input' => 'required name=search_term_string',
    ],
];
if (!empty($schemaData)) {
    $schemaBlocks = array_merge($schemaBlocks, isset($schemaData[0]) ? $schemaData : [$schemaData]);
} else {
    $pageSchema = [
        '@context' => 'https://schema.org',
        '@type' => $schemaType,
        'name' => $seoTitle,
        'description' => $seoDescription,
        'url' => $seoUrl,
    ];
    if (!empty($seoPublished)) {
        $pageSchema['datePublished'] = $seoPublished;
    }
    if (!empty($seoModified)) {
        $pageSchema['dateModified'] = $seoModified;
    }
    if (!empty($seoImage)) {
        $pageSchema['image'] = [$seoImage];
    }
    if (!empty($seoAuthor)) {
        $pageSchema['author'] = [
            '@type' => 'Organization',
            'name' => $seoAuthor,
        ];
    }
    $schemaBlocks[] = $pageSchema;
}
if (!empty($breadcrumbs)) {
    $breadcrumbItems = [];
    foreach ($breadcrumbs as $index => $crumb) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $crumb['name'],
            'item' => $crumb['url'] ?? $seoUrl,
        ];
    }
    $schemaBlocks[] = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbItems,
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($seoTitle) ?></title>
    <meta name="description" content="<?= e($seoDescription) ?>">
    <meta name="keywords" content="finance, investing, budgeting, wealth, money management, financial planning">
    <meta name="author" content="<?= e($seoAuthor) ?>">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="<?= e($seoUrl) ?>">
    <?php if (!empty($ampUrl)): ?>
        <link rel="amphtml" href="<?= e($ampUrl) ?>">
    <?php endif; ?>
    <meta property="og:title" content="<?= e($seoTitle) ?>">
    <meta property="og:description" content="<?= e($seoDescription) ?>">
    <meta property="og:type" content="<?= e($seoType) ?>">
    <meta property="og:url" content="<?= e($seoUrl) ?>">
    <meta property="og:site_name" content="<?= e($siteName) ?>">
    <meta property="og:locale" content="en_US">
    <?php if (!empty($seoImage)): ?>
        <meta property="og:image" content="<?= e($seoImage) ?>">
        <meta name="twitter:image" content="<?= e($seoImage) ?>">
    <?php endif; ?>
    <?php if (!empty($seoPublished)): ?>
        <meta property="article:published_time" content="<?= e($seoPublished) ?>">
    <?php endif; ?>
    <?php if (!empty($seoModified)): ?>
        <meta property="article:modified_time" content="<?= e($seoModified) ?>">
    <?php endif; ?>
    <?php if (!empty($seoSection)): ?>
        <meta property="article:section" content="<?= e($seoSection) ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="<?= !empty($seoImage) ? 'summary_large_image' : 'summary' ?>">
    <meta name="twitter:title" content="<?= e($seoTitle) ?>">
    <meta name="twitter:description" content="<?= e($seoDescription) ?>">
    <link rel="stylesheet" href="<?= asset_url('assets/css/style.css') ?>">
    <script defer src="<?= asset_url('assets/js/app.js') ?>" nonce="<?= e($nonce) ?>"></script>
    <?php foreach ($schemaBlocks as $schemaBlock): ?>
        <script type="application/ld+json" nonce="<?= e($nonce) ?>"><?= json_encode($schemaBlock, JSON_UNESCAPED_SLASHES) ?></script>
    <?php endforeach; ?>
</head>
<body class="dark">
    <header class="site-header">
        <div class="container">
            <a class="logo" href="<?= base_url('/') ?>"><?= e($siteName) ?></a>
            <nav>
                <a href="<?= base_url('/start-here') ?>">Start Here</a>
                <a href="<?= base_url('/blog') ?>">Guides</a>
                <a href="<?= base_url('/categories') ?>">Categories</a>
                <a href="<?= base_url('/tools') ?>">Tools</a>
            </nav>
            <div class="search">
                <input id="live-search" type="search" placeholder="Search guides...">
                <div id="search-results" class="search-results"></div>
            </div>
        </div>
    </header>
    <main>
        <?php if (!empty($breadcrumbs)): ?>
            <nav class="breadcrumbs" aria-label="Breadcrumb">
                <div class="container">
                    <?php foreach ($breadcrumbs as $index => $crumb): ?>
                        <?php if ($index > 0): ?>
                            <span class="crumb-separator">/</span>
                        <?php endif; ?>
                        <?php if (!empty($crumb['url']) && $index < count($breadcrumbs) - 1): ?>
                            <a href="<?= e($crumb['url']) ?>"><?= e($crumb['name']) ?></a>
                        <?php else: ?>
                            <span><?= e($crumb['name']) ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </nav>
        <?php endif; ?>
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
