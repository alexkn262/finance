<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Contact us</h1>
        <p>We respond within 1-2 business days.</p>
    </div>
</section>
<section class="comments">
    <div class="container">
        <form method="post" action="<?= base_url('/contact') ?>" class="comment-form">
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
            <label>Name <input type="text" name="name" required></label>
            <label>Email <input type="email" name="email" required></label>
            <label>Subject <input type="text" name="subject"></label>
            <label>Message <textarea name="message" rows="5" required></textarea></label>
            <button class="btn" type="submit">Send message</button>
        </form>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
