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
        :root{color-scheme:dark;}
        body{margin:0;font-family:'Segoe UI',sans-serif;background:#0b0b12;color:#f5f5f5;}
        a{color:#e50914;text-decoration:none;}
        .container{max-width:900px;margin:0 auto;padding:24px;}
        .hero{padding:32px 0;background:radial-gradient(circle at top, rgba(229,9,20,0.2), transparent 60%);}
        .card{background:#1b1b2a;padding:24px;border-radius:18px;box-shadow:0 12px 32px rgba(0,0,0,0.35);}
        .meta{display:flex;gap:12px;flex-wrap:wrap;color:#9da5b4;font-size:0.9rem;margin-top:8px;}
        .article-image{margin:24px 0;border-radius:16px;overflow:hidden;}
        .article-content{line-height:1.8;font-size:1.05rem;}
        .article-content h2,.article-content h3{margin-top:1.5rem;}
        .share-row{display:flex;gap:12px;flex-wrap:wrap;margin-top:16px;}
        .share-pill{background:#151521;border-radius:999px;padding:8px 16px;color:#fff;}
        .footer{margin-top:32px;color:#9da5b4;font-size:0.85rem;text-align:center;}
    </style>
</head>
<body>
    <header class="hero">
        <div class="container">
            <h1><?= e($article['title']) ?></h1>
            <div class="meta">
                <span><?= e($article['category_name'] ?? 'General') ?></span>
                <span><?= date('M d, Y', (int) $article['created_at']) ?></span>
                <span>By Finance Editor</span>
            </div>
        </div>
    </header>
    <main class="container">
        <?php if (!empty($article['featured_image'])): ?>
            <div class="article-image">
                <amp-img src="<?= e($article['featured_image']) ?>" width="800" height="450" layout="responsive" alt="<?= e($article['title']) ?>"></amp-img>
            </div>
        <?php endif; ?>
        <div class="card article-content">
            <?= $article['content_html'] ?>
        </div>
        <div class="share-row">
            <a class="share-pill" href="https://twitter.com/intent/tweet?url=<?= urlencode(base_url('/blog/' . $article['slug'])) ?>">Share on X</a>
            <a class="share-pill" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(base_url('/blog/' . $article['slug'])) ?>">Share on Facebook</a>
        </div>
        <p class="footer">AMP view optimized for speed while matching the main article design.</p>
    </main>
</body>
</html>
