<?php ob_start(); ?>
<section class="admin-header">
    <h1>Analytics for <?= e($page) ?></h1>
</section>
<section class="admin-chart">
    <div class="chart">
        <?php foreach ($trends as $trend): ?>
            <div class="chart-bar" style="--value: <?= (int) $trend['views'] ?>">
                <span><?= e($trend['day']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Analytics Detail']); ?>
