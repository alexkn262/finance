<?php ob_start(); ?>
<section class="admin-header">
    <h1>Ads placements</h1>
</section>
<form method="post" action="<?= base_url('/admin/ads') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <label>Homepage ads code <textarea name="ads_index" rows="4"><?= e($settings['ads_index'] ?? '') ?></textarea></label>
    <label>Article ads code <textarea name="ads_article" rows="4"><?= e($settings['ads_article'] ?? '') ?></textarea></label>
    <label>Tools ads code <textarea name="ads_tools" rows="4"><?= e($settings['ads_tools'] ?? '') ?></textarea></label>
    <label>Category ads code <textarea name="ads_category" rows="4"><?= e($settings['ads_category'] ?? '') ?></textarea></label>
    <label>Sidebar ads code <textarea name="ads_sidebar" rows="4"><?= e($settings['ads_sidebar'] ?? '') ?></textarea></label>
    <button class="btn" type="submit">Save ads</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Ads']); ?>
