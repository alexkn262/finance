<?php ob_start(); ?>
<?php
$seoTitle = $seoTitle ?? 'Privacy Policy';
$seoDescription = $seoDescription ?? 'Read how we protect your data and keep analytics privacy-first.';
$seoImage = $seoImage ?? config('seo_image');
?>
<section class="hero compact">
    <div class="container">
        <h1>Privacy policy</h1>
        <p>We respect your privacy and protect your data.</p>
    </div>
</section>
<section class="article-content">
    <div class="container">
        <p>We collect minimal analytics and newsletter information to improve your experience. You can unsubscribe anytime.</p>
        <p>Contact us for any data requests or questions.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
