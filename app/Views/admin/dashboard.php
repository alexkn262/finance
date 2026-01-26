<?php ob_start(); ?>
<section class="admin-header">
    <div>
        <h1>Dashboard</h1>
        <p>Overview of your finance platform performance.</p>
    </div>
    <div class="admin-actions">
        <a class="btn" href="<?= base_url('/admin/articles/create') ?>">New article</a>
    </div>
</section>
<section class="admin-metrics">
    <div class="metric"><h3>Articles</h3><strong><?= (int) $articles ?></strong></div>
    <div class="metric"><h3>Categories</h3><strong><?= (int) $categories ?></strong></div>
    <div class="metric"><h3>Comments</h3><strong><?= (int) $comments ?></strong></div>
    <div class="metric"><h3>Page views</h3><strong><?= (int) $page_views ?></strong></div>
    <div class="metric"><h3>Unique visitors</h3><strong><?= (int) $unique_visitors ?></strong></div>
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
<section class="admin-table-wrap">
    <h2>Recent articles</h2>
    <table class="admin-table">
        <thead>
            <tr><th>Title</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($recentArticles as $article): ?>
                <tr>
                    <td><?= e($article['title']) ?></td>
                    <td><?= e($article['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Dashboard']); ?>
