<?php ob_start(); ?>
<?php
$seoTitle = $seoTitle ?? 'Finance Guides';
$seoDescription = $seoDescription ?? 'Explore finance guides built for long-term wealth.';
$seoImage = $seoImage ?? (config('seo_image') ?: ($articles[0]['featured_image'] ?? ''));
?>
<section class="hero compact">
    <div class="container">
        <h1>Finance guides</h1>
        <p>Explore finance guides built for long-term wealth.</p>
    </div>
</section>
<?php if (!empty($categoryInfo)): ?>
    <section class="article-content">
        <div class="container">
            <div class="grid">
                <div class="card">
                    <h2><?= e($categoryInfo['name']) ?></h2>
                    <p><?= e($categoryInfo['seo_description'] ?? 'Category insights and curated guides.') ?></p>
                    <p>Total guides: <?= (int) $totalArticles ?></p>
                    <a class="btn" href="<?= base_url('/categories') ?>">Browse all categories</a>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<section class="blog-list">
    <div class="container">
        <div class="filters">
            <a class="chip<?= $currentCategory ? '' : ' active' ?>" href="<?= base_url('/blog') ?>">All</a>
            <?php foreach ($categories as $category): ?>
                <a class="chip<?= $currentCategory === $category['slug'] ? ' active' : '' ?>" href="<?= base_url('/blog?category=' . $category['slug']) ?>"><?= e($category['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <div class="grid grid-2">
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
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="<?= base_url('/blog?page=' . ($page - 1)) ?>">Previous</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="<?= base_url('/blog?page=' . ($page + 1)) ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
