<?php ob_start(); ?>
<section class="admin-header">
    <h1>Analytics overview</h1>
</section>
<section class="admin-metrics">
    <div class="metric">
        <h3>Top pages (24h)</h3>
        <ul>
            <?php foreach ($pages as $page): ?>
                <li><?= e($page['page']) ?> — <?= (int) $page['views'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="metric">
        <h3>Top pages (all time)</h3>
        <ul>
            <?php foreach ($topAll as $page): ?>
                <li><?= e($page['page']) ?> — <?= (int) $page['views'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<section class="admin-chart">
    <h2>Unique visitors (7 days)</h2>
    <div class="chart">
        <?php foreach ($trends as $trend): ?>
            <div class="chart-bar" style="--value: <?= (int) $trend['views'] ?>">
                <span><?= e($trend['day']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Analytics']); ?>
