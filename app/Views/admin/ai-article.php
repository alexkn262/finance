<?php ob_start(); ?>
<section class="admin-header">
    <h1>AI article studio</h1>
    <p>Generate a long-form finance article and refine with rewrites.</p>
</section>
<form method="post" action="<?= base_url('/admin/articles/ai') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <label>Keyword <input type="text" name="keyword" required></label>
    <button class="btn" type="submit">Generate</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'AI Studio']); ?>
