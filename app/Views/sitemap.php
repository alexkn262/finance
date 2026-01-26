<?php header('Content-Type: application/xml'); ?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= e(base_url('/')) ?></loc>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= e(base_url('/blog')) ?></loc>
        <priority>0.8</priority>
    </url>
    <?php foreach ($articles as $article): ?>
        <url>
            <loc><?= e(base_url('/blog/' . $article['slug'])) ?></loc>
            <priority>1.0</priority>
        </url>
    <?php endforeach; ?>
</urlset>
