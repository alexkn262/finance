<?php header('Content-Type: text/html'); ?>
<!doctype html>
<html amp lang="en">
<head>
    <meta charset="utf-8">
    <title><?= e($article['title']) ?></title>
    <link rel="canonical" href="<?= base_url('/blog/' . $article['slug']) ?>">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <style amp-custom>
        body{font-family:Arial,sans-serif;padding:16px;background:#0b0b12;color:#fff;}
        a{color:#e50914;}
        .meta{color:#9da5b4;margin-bottom:16px;}
    </style>
</head>
<body>
    <h1><?= e($article['title']) ?></h1>
    <div class="meta"><?= e($article['category_name'] ?? 'General') ?> · <?= date('M d, Y', (int) $article['created_at']) ?></div>
    <?php if (!empty($article['featured_image'])): ?>
        <amp-img src="<?= e($article['featured_image']) ?>" width="800" height="450" layout="responsive" alt="<?= e($article['title']) ?>"></amp-img>
    <?php endif; ?>
    <div><?= $article['content_html'] ?></div>
</body>
</html>
