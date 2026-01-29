<?php ob_start(); ?>
<section class="admin-header">
    <h1>Rewrite options</h1>
    <p>Select a rewrite and continue editing before publishing.</p>
</section>
<form method="post" action="<?= base_url('/admin/articles/create') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <label>Title <input type="text" name="title" value=""></label>
    <label>Slug <input type="text" name="slug" value=""></label>
    <label>Featured image URL <input type="text" name="featured_image" value="<?= e($featured_image ?? '') ?>"></label>
    <label>Content (HTML)
        <textarea name="content_html" rows="6"></textarea>
    </label>
    <label>Rewrite options
        <select name="rewrite_choice">
            <?php foreach ($rewrites as $rewrite): ?>
                <option value="<?= e($rewrite) ?>"><?= e($rewrite) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>SEO description <input type="text" name="seo_description" value=""></label>
    <label>Status
        <select name="status">
            <option value="draft">Draft</option>
            <option value="published">Published</option>
        </select>
    </label>
    <button class="btn" type="submit">Save article</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'AI Rewrites']); ?>
