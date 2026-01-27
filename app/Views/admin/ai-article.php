<?php ob_start(); ?>
<section class="admin-header">
    <h1>AI article studio</h1>
    <p>Generate a long-form finance article and refine with rewrites.</p>
</section>
<?php if (!empty($error)): ?>
    <div class="metric"><?= e($error) ?></div>
<?php endif; ?>
<form method="post" action="<?= base_url('/admin/articles/ai') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <label>Keyword <input type="text" name="keyword" maxlength="120" required></label>
    <button class="btn" type="submit">Generate</button>
</form>
<div class="article-content">
    <p>Tip: We synthesize insights from top finance resources and generate a featured image suggestion.</p>
</div>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'AI Studio']); ?>
