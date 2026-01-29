<?php ob_start(); ?>
<section class="admin-header">
    <h1>Create article</h1>
</section>
<form method="post" action="<?= base_url('/admin/articles/create') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <label>Title <input type="text" name="title" required></label>
    <label>Slug <input type="text" name="slug" required></label>
    <label>Category
        <select name="category_id">
            <option value="">None</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Tags
        <select name="tags[]" multiple>
            <?php foreach ($tags as $tag): ?>
                <option value="<?= (int) $tag['id'] ?>"><?= e($tag['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Featured image URL <input type="text" name="featured_image"></label>
    <label>SEO title <input type="text" name="seo_title"></label>
    <label>SEO description <input type="text" name="seo_description"></label>
    <label>Content (HTML)
        <textarea name="content_html" rows="10"></textarea>
    </label>
    <label>Status
        <select name="status">
            <option value="draft">Draft</option>
            <option value="published">Published</option>
        </select>
    </label>
    <button class="btn" type="submit">Save article</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Create Article']); ?>
