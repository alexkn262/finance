<?php ob_start(); ?>
<section class="admin-header">
    <h1>Edit tag</h1>
</section>
<form method="post" action="<?= base_url('/admin/tags/edit') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <input type="hidden" name="id" value="<?= (int) ($tag['id'] ?? 0) ?>">
    <label>Name <input type="text" name="name" value="<?= e($tag['name'] ?? '') ?>" required></label>
    <label>Slug <input type="text" name="slug" value="<?= e($tag['slug'] ?? '') ?>" required></label>
    <label>SEO title <input type="text" name="seo_title" value="<?= e($tag['seo_title'] ?? '') ?>"></label>
    <label>SEO description <input type="text" name="seo_description" value="<?= e($tag['seo_description'] ?? '') ?>"></label>
    <button class="btn" type="submit">Update tag</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Edit Tag']); ?>
