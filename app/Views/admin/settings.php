<?php ob_start(); ?>
<section class="admin-header">
    <h1>Settings</h1>
</section>
<form method="post" action="<?= base_url('/admin/settings') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <label>Site name <input type="text" name="site_name" value="<?= e($settings['site_name'] ?? '') ?>"></label>
    <label>Site URL <input type="text" name="site_url" value="<?= e($settings['site_url'] ?? '') ?>"></label>
    <label>SEO title <input type="text" name="seo_title" value="<?= e($settings['seo_title'] ?? '') ?>"></label>
    <label>SEO description <input type="text" name="seo_description" value="<?= e($settings['seo_description'] ?? '') ?>"></label>
    <button class="btn" type="submit">Save settings</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Settings']); ?>
