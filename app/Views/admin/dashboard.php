<?php ob_start(); ?>
<section class="admin-header">
    <h1>Dashboard</h1>
    <p>Overview of your finance platform performance.</p>
</section>
<section class="admin-metrics">
    <div class="metric">Articles <strong><?= (int) $articles ?></strong></div>
    <div class="metric">Categories <strong><?= (int) $categories ?></strong></div>
    <div class="metric">Comments <strong><?= (int) $comments ?></strong></div>
    <div class="metric">Page views <strong><?= (int) $page_views ?></strong></div>
    <div class="metric">Unique visitors <strong><?= (int) $unique_visitors ?></strong></div>
</section>
<section class="admin-chart">
    <h2>Traffic trends</h2>
    <div class="chart">
        <?php foreach ($trends as $trend): ?>
            <div class="chart-bar" style="--value: <?= (int) $trend['views'] ?>">
                <span><?= e($trend['day']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Dashboard']); ?>
