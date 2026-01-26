<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Terms of service</h1>
        <p>Guidelines for using our finance platform.</p>
    </div>
</section>
<section class="article-content">
    <div class="container">
        <p>All content is for educational purposes and not financial advice.</p>
        <p>By using this site you agree to these terms.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
