<?php ob_start(); ?>
<section class="admin-header">
    <h1>AI draft</h1>
</section>
<article class="admin-article">
    <h2><?= e($article['title']) ?></h2>
    <p><?= e($article['meta_description']) ?></p>
    <div class="preview">
        <?= $article['content'] ?>
    </div>
</article>
<form method="post" action="<?= base_url('/admin/articles/rewrite') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <textarea name="content" rows="6"><?= e($article['content']) ?></textarea>
    <button class="btn" type="submit">Generate rewrites</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'AI Draft']); ?>
