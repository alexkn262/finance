<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Finance guides</h1>
        <p>Explore finance guides built for long-term wealth.</p>
    </div>
</section>
<section class="blog-list">
    <div class="container">
        <div class="filters">
            <a class="chip<?= $currentCategory ? '' : ' active' ?>" href="<?= base_url('/blog') ?>">All</a>
            <?php foreach ($categories as $category): ?>
                <a class="chip<?= $currentCategory === $category['slug'] ? ' active' : '' ?>" href="<?= base_url('/blog?category=' . $category['slug']) ?>"><?= e($category['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <div class="grid">
            <?php foreach ($articles as $article): ?>
                <article class="card">
                    <h3><?= e($article['title']) ?></h3>
                    <p><?= e($article['seo_description'] ?? '') ?></p>
                    <a href="<?= base_url('/blog/' . $article['slug']) ?>">Read guide</a>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="<?= base_url('/blog?page=' . ($page - 1)) ?>">Previous</a>
            <?php endif; ?>
            <a href="<?= base_url('/blog?page=' . ($page + 1)) ?>">Next</a>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
