<?php ob_start(); ?>
<section class="admin-header">
    <h1>Edit article</h1>
</section>
<form method="post" action="<?= base_url('/admin/articles/edit') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <input type="hidden" name="id" value="<?= (int) ($article['id'] ?? 0) ?>">
    <label>Title <input type="text" name="title" value="<?= e($article['title'] ?? '') ?>" required></label>
    <label>Slug <input type="text" name="slug" value="<?= e($article['slug'] ?? '') ?>" required></label>
    <label>Category
        <select name="category_id">
            <option value="">None</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>" <?= ($article['category_id'] ?? null) == $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Featured image URL <input type="text" name="featured_image" value="<?= e($article['featured_image'] ?? '') ?>"></label>
    <label>SEO title <input type="text" name="seo_title" value="<?= e($article['seo_title'] ?? '') ?>"></label>
    <label>SEO description <input type="text" name="seo_description" value="<?= e($article['seo_description'] ?? '') ?>"></label>
    <label>Content (HTML)
        <textarea name="content_html" rows="10"><?= e($article['content_html'] ?? '') ?></textarea>
    </label>
    <label>Status
        <select name="status">
            <option value="draft" <?= ($article['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
        </select>
    </label>
    <button class="btn" type="submit">Update article</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Edit Article']); ?>
