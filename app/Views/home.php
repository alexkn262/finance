<?php ob_start(); ?>
<?php
$seoTitle = $seoTitle ?? config('seo_home', 'Finance Education Platform');
$seoDescription = $seoDescription ?? config('seo_description', 'Modern finance education, tools, and guides.');
$seoImage = $seoImage ?? (config('seo_image') ?: ($articles[0]['featured_image'] ?? ''));
?>
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Master your money with clarity, confidence, and strategy.</h1>
            <p>Premium finance education, tools, and playbooks designed to build long-term wealth.</p>
            <a class="btn primary" href="<?= base_url('/start-here') ?>">Start Here</a>
        </div>
        <div class="hero-cards">
            <div class="card highlight">Personal finance mastery</div>
            <div class="card">Smart investing frameworks</div>
            <div class="card">Wealth-building systems</div>
        </div>
    </div>
</section>
<section class="featured">
    <div class="container">
        <h2>Featured guides</h2>
        <div class="grid">
            <?php foreach ($articles as $article): ?>
                <article class="card">
                    <div class="card-media" style="background-image: url('<?= e($article['featured_image'] ?? 'https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?auto=format&fit=crop&w=800&q=80') ?>')"></div>
                    <div class="card-body">
                        <h3><?= e($article['title']) ?></h3>
                        <p><?= e(excerpt($article['seo_description'] ?: ($article['content_html'] ?? ''))) ?></p>
                        <small><a href="<?= base_url('/blog?category=' . ($article['category_slug'] ?? '')) ?>" class="meta-link"><?= e($article['category_name'] ?? 'General') ?></a> · <?= date('M d, Y', (int) $article['created_at']) ?> · Views: <?= (int) ($article['view_count'] ?? 0) ?></small>
                        <a href="<?= base_url('/blog/' . $article['slug']) ?>">Read guide</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="featured">
    <div class="container">
        <h2>Popular tools</h2>
        <div class="grid">
            <?php foreach ($tools as $tool): ?>
                <a class="card" href="<?= base_url($tool['url']) ?>">
                    <h3><?= e($tool['title']) ?></h3>
                    <p>Interactive calculator built to maximize learning time.</p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="categories">
    <div class="container">
        <h2>Explore categories</h2>
        <div class="grid">
            <?php foreach ($categories as $category): ?>
                <div class="card">
                    <div class="card-media" style="background-image: url('<?= e($category['featured_image'] ?? 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=800&q=80') ?>')"></div>
                    <h3><?= e($category['name']) ?></h3>
                    <a href="<?= base_url('/blog?category=' . $category['slug']) ?>">View</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
