<?php ob_start(); ?>
<section class="admin-header">
    <h1>Analytics overview</h1>
</section>
<table class="admin-table">
    <thead>
        <tr><th>Page</th><th>Views</th><th>Unique visitors</th><th>Details</th></tr>
    </thead>
    <tbody>
        <?php foreach ($pages as $page): ?>
            <tr>
                <td><?= e($page['page']) ?></td>
                <td><?= (int) $page['views'] ?></td>
                <td><?= (int) $page['unique_visitors'] ?></td>
                <td><a href="<?= base_url('/admin/analytics/page?page=' . urlencode($page['page'])) ?>">View trends</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Analytics']); ?>
