<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1><?= e($article['title']) ?></h1>
        <p><?= e($article['seo_description'] ?? '') ?></p>
        <div class="article-meta">
            <span><?= e($article['category_name'] ?? 'General') ?></span>
            <span><?= date('M d, Y', (int) $article['created_at']) ?></span>
        </div>
    </div>
</section>
<section class="article-content">
    <div class="container">
        <?php if (!empty($article['featured_image'])): ?>
            <img class="article-image" src="<?= e($article['featured_image']) ?>" alt="<?= e($article['title']) ?>">
        <?php endif; ?>
        <?= $article['content_html'] ?>
        <div class="article-actions">
            <?php if ($prev): ?>
                <a rel="prev" href="<?= base_url('/blog/' . $prev['slug']) ?>">← <?= e($prev['title']) ?></a>
            <?php endif; ?>
            <?php if ($next): ?>
                <a rel="next" href="<?= base_url('/blog/' . $next['slug']) ?>"><?= e($next['title']) ?> →</a>
            <?php endif; ?>
        </div>
        <div class="article-share">
            <span>Share:</span>
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()) ?>">Twitter</a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>">Facebook</a>
            <a href="mailto:?subject=<?= urlencode($article['title']) ?>&body=<?= urlencode(current_url()) ?>">Email</a>
            <a href="<?= e($ampUrl) ?>">AMP Version</a>
        </div>
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
