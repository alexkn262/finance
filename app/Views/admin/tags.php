<?php ob_start(); ?>
<section class="admin-header">
    <h1>Tags</h1>
</section>
<form method="post" action="<?= base_url('/admin/tags') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <label>Name <input type="text" name="name" required></label>
    <label>Slug <input type="text" name="slug" required></label>
    <label>SEO title <input type="text" name="seo_title"></label>
    <label>SEO description <input type="text" name="seo_description"></label>
    <button class="btn" type="submit">Add tag</button>
</form>
<table class="admin-table">
    <thead>
        <tr><th>Name</th><th>Slug</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($tags as $tag): ?>
            <tr>
                <td><?= e($tag['name']) ?></td>
                <td><?= e($tag['slug']) ?></td>
                <td><a href="<?= base_url('/admin/tags/edit?id=' . (int) $tag['id']) ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Tags']); ?>
