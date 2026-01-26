<?php ob_start(); ?>
<section class="admin-header">
    <h1>Settings</h1>
</section>
<form method="post" action="<?= base_url('/admin/settings') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <label>Site name <input type="text" name="site_name" value="<?= e($settings['site_name'] ?? '') ?>"></label>
    <label>Site URL <input type="text" name="site_url" value="<?= e($settings['site_url'] ?? '') ?>"></label>
    <label>SEO title (default) <input type="text" name="seo_title" value="<?= e($settings['seo_title'] ?? '') ?>"></label>
    <label>SEO description (default) <input type="text" name="seo_description" value="<?= e($settings['seo_description'] ?? '') ?>"></label>
    <label>SEO title (home) <input type="text" name="seo_home" value="<?= e($settings['seo_home'] ?? '') ?>"></label>
    <label>SEO title (tools) <input type="text" name="seo_tools" value="<?= e($settings['seo_tools'] ?? '') ?>"></label>
    <label>GA4 Tracking ID <input type="text" name="ga4_id" value="<?= e($settings['ga4_id'] ?? '') ?>"></label>
    <label>AdSense Code <textarea name="adsense_code" rows="4"><?= e($settings['adsense_code'] ?? '') ?></textarea></label>
    <label>OpenAI API Key <input type="text" name="openai_api_key" value="<?= e($settings['openai_api_key'] ?? '') ?>"></label>
    <label>Paraphrase API 1 <input type="text" name="paraphrase_api_1" value="<?= e($settings['paraphrase_api_1'] ?? '') ?>"></label>
    <label>Paraphrase API 2 <input type="text" name="paraphrase_api_2" value="<?= e($settings['paraphrase_api_2'] ?? '') ?>"></label>
    <label>Paraphrase API 3 <input type="text" name="paraphrase_api_3" value="<?= e($settings['paraphrase_api_3'] ?? '') ?>"></label>
    <label>Paraphrase API 4 <input type="text" name="paraphrase_api_4" value="<?= e($settings['paraphrase_api_4'] ?? '') ?>"></label>
    <label><input type="checkbox" name="clear_cache" value="1"> Clear cache</label>
    <button class="btn" type="submit">Save settings</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Settings']); ?>
