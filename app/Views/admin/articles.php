<?php ob_start(); ?>
<section class="admin-header">
    <h1>Articles</h1>
    <a class="btn" href="<?= base_url('/admin/articles/create') ?>">New article</a>
</section>
<table class="admin-table">
    <thead>
        <tr><th>Title</th><th>Status</th><th>Created</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $article): ?>
            <tr>
                <td><?= e($article['title']) ?></td>
                <td><?= e($article['status']) ?></td>
                <td><?= date('Y-m-d', (int) $article['created_at']) ?></td>
                <td><a href="<?= base_url('/admin/articles/edit?id=' . (int) $article['id']) ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Articles']); ?>
