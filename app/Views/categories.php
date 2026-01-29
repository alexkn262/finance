<?php ob_start(); ?>
<?php
$seoTitle = $seoTitle ?? 'Finance Categories';
$seoDescription = $seoDescription ?? 'Browse every finance category with featured guides and deep-dive learning paths.';
$seoImage = $seoImage ?? (config('seo_image') ?: ($categories[0]['featured_image'] ?? ''));
?>
<section class="hero compact">
    <div class="container">
        <h1>Categories</h1>
        <p>Browse every finance category with dedicated learning paths and curated guides.</p>
    </div>
</section>
<section class="blog-list">
    <div class="container">
        <div class="grid grid-2">
            <?php foreach ($categories as $category): ?>
                <article class="card">
                    <div class="card-media" style="background-image: url('<?= e($category['featured_image'] ?? 'https://images.unsplash.com/photo-1489515217757-5fd1be406fef?auto=format&fit=crop&w=800&q=80') ?>')"></div>
                    <div class="card-body">
                        <h3><?= e($category['name']) ?></h3>
                        <p><?= e($category['seo_description'] ?? 'Category insights and curated guides.') ?></p>
                        <small>Total guides: <?= (int) ($category['article_count'] ?? 0) ?></small>
                        <a href="<?= base_url('/blog?category=' . $category['slug']) ?>">View category</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
