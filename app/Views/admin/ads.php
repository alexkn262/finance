<?php ob_start(); ?>
<section class="admin-header">
    <h1>Ads placements</h1>
    <p>Define manual ad units for specific placements. Auto ads code lives in Settings.</p>
</section>
<form method="post" action="<?= base_url('/admin/ads') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <label>Homepage hero (970x250) <textarea name="ads_index" rows="4"><?= e($settings['ads_index'] ?? '') ?></textarea></label>
    <label>Article header (728x90) <textarea name="ads_article" rows="4"><?= e($settings['ads_article'] ?? '') ?></textarea></label>
    <label>Tools sidebar (300x250) <textarea name="ads_tools" rows="4"><?= e($settings['ads_tools'] ?? '') ?></textarea></label>
    <label>Category grid (336x280) <textarea name="ads_category" rows="4"><?= e($settings['ads_category'] ?? '') ?></textarea></label>
    <label>Sidebar sticky (300x600) <textarea name="ads_sidebar" rows="4"><?= e($settings['ads_sidebar'] ?? '') ?></textarea></label>
    <button class="btn" type="submit">Save ads</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Ads']); ?>
