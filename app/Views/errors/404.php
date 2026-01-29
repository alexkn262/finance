<?php ob_start(); ?>
<?php
$seoTitle = $seoTitle ?? 'Page not found';
$seoDescription = $seoDescription ?? 'The page you requested could not be found.';
$seoImage = $seoImage ?? config('seo_image');
?>
<section class="hero compact">
    <div class="container">
        <h1>Page not found</h1>
        <p>The page you requested could not be found.</p>
        <a class="btn" href="<?= base_url('/') ?>">Return home</a>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
