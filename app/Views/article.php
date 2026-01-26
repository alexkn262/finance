<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1><?= e($article['title']) ?></h1>
        <p><?= e($article['seo_description'] ?? '') ?></p>
    </div>
</section>
<section class="article-content">
    <div class="container">
        <?= $article['content_html'] ?>
    </div>
</section>
<section class="comments">
    <div class="container">
        <h2>Comments</h2>
        <form method="post" action="<?= base_url('/comment') ?>" class="comment-form">
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
            <input type="hidden" name="article_id" value="<?= (int) $article['id'] ?>">
            <label>Name <input type="text" name="author_name" required></label>
            <label>Email <input type="email" name="author_email" required></label>
            <label>Comment <textarea name="content" rows="4" required></textarea></label>
            <button class="btn" type="submit">Submit</button>
        </form>
        <div class="comment-list">
            <?php foreach ($comments as $comment): ?>
                <div class="card">
                    <strong><?= e($comment['author_name']) ?></strong>
                    <p><?= e($comment['content']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
