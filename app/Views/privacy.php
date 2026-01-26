<?php ob_start(); ?>
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
