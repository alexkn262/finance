<?php ob_start(); ?>
<section class="admin-header">
    <h1>Categories</h1>
</section>
<form method="post" action="<?= base_url('/admin/categories') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <label>Name <input type="text" name="name" required></label>
    <label>Slug <input type="text" name="slug" required></label>
    <label>Parent ID <input type="number" name="parent_id"></label>
    <label>Featured image URL <input type="text" name="featured_image"></label>
    <label>SEO title <input type="text" name="seo_title"></label>
    <label>SEO description <input type="text" name="seo_description"></label>
    <button class="btn" type="submit">Add category</button>
</form>
<table class="admin-table">
    <thead>
        <tr><th>Name</th><th>Slug</th><th>Parent</th><th>Featured image</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= e($category['name']) ?></td>
                <td><?= e($category['slug']) ?></td>
                <td><?= e((string) $category['parent_id']) ?></td>
                <td><?= e((string) ($category['featured_image'] ?? '')) ?></td>
                <td><a href="<?= base_url('/admin/categories/edit?id=' . (int) $category['id']) ?>">Edit</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Categories']); ?>
