<?php ob_start(); ?>
<section class="admin-header">
    <h1>Edit category</h1>
</section>
<form method="post" action="<?= base_url('/admin/categories/edit') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <input type="hidden" name="id" value="<?= (int) ($category['id'] ?? 0) ?>">
    <label>Name <input type="text" name="name" value="<?= e($category['name'] ?? '') ?>" required></label>
    <label>Slug <input type="text" name="slug" value="<?= e($category['slug'] ?? '') ?>" required></label>
    <label>Parent ID <input type="number" name="parent_id" value="<?= e((string) ($category['parent_id'] ?? '')) ?>"></label>
    <label>SEO title <input type="text" name="seo_title" value="<?= e($category['seo_title'] ?? '') ?>"></label>
    <label>SEO description <input type="text" name="seo_description" value="<?= e($category['seo_description'] ?? '') ?>"></label>
    <button class="btn" type="submit">Update category</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Edit Category']); ?>
