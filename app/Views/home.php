<?php ob_start(); ?>
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
                    <h3><?= e($article['title']) ?></h3>
                    <p><?= e($article['seo_description'] ?? '') ?></p>
                    <a href="<?= base_url('/blog/' . $article['slug']) ?>">Read guide</a>
                </article>
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
                    <h3><?= e($category['name']) ?></h3>
                    <a href="<?= base_url('/blog?category=' . $category['slug']) ?>">View</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="newsletter">
    <div class="container">
        <h2>Get the weekly finance brief</h2>
        <p>Actionable insights, tools, and new guides delivered to your inbox.</p>
        <form method="post" action="<?= base_url('/newsletter') ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="email" name="email" placeholder="you@example.com" required>
            <button class="btn" type="submit">Subscribe</button>
        </form>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
