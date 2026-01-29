<?php ob_start(); ?>
<?php
$seoTitle = $seoTitle ?? 'Start Here: Your Finance Roadmap';
$seoDescription = $seoDescription ?? 'Beginner to advanced finance paths with curated categories and step-by-step guides.';
$seoImage = $seoImage ?? config('seo_image');
?>
<section class="hero compact">
    <div class="container">
        <h1>Start here: build your financial journey.</h1>
        <p>Choose your path and follow a curated learning sequence.</p>
    </div>
</section>
<section class="paths">
    <div class="container">
        <div class="grid">
            <div class="card">
                <h3>Beginner</h3>
                <ul>
                    <li>Budget foundations</li>
                    <li>Debt payoff strategy</li>
                    <li>Emergency fund playbook</li>
                </ul>
            </div>
            <div class="card">
                <h3>Intermediate</h3>
                <ul>
                    <li>Index investing</li>
                    <li>Tax efficiency</li>
                    <li>Automated savings</li>
                </ul>
            </div>
            <div class="card">
                <h3>Advanced</h3>
                <ul>
                    <li>Portfolio optimization</li>
                    <li>Real estate analysis</li>
                    <li>Exit strategies</li>
                </ul>
            </div>
        </div>
    </div>
</section>
<?php if (!empty($categories)): ?>
    <section class="categories">
        <div class="container">
            <div class="section-title">
                <h2>Explore categories</h2>
                <p>Jump into each topic area with curated guides and learning tracks.</p>
            </div>
            <div class="grid grid-2">
                <?php foreach ($categories as $category): ?>
                    <article class="card">
                        <div class="card-media" style="background-image: url('<?= e($category['featured_image'] ?? 'https://images.unsplash.com/photo-1489515217757-5fd1be406fef?auto=format&fit=crop&w=800&q=80') ?>')"></div>
                        <div class="card-body">
                            <h3><?= e($category['name']) ?></h3>
                            <p><?= e($category['seo_description'] ?? 'Category insights and curated guides.') ?></p>
                            <a href="<?= base_url('/blog?category=' . $category['slug']) ?>">View category</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
