<?php ob_start(); ?>
<?php
$seoImage = $seoImage ?? ($article['featured_image'] ?? '');
$seoDescription = $seoDescription ?? ($article['seo_description'] ?? '');
?>
<section class="hero compact">
    <div class="container">
        <h1><?= e($article['title']) ?></h1>
        <p><?= e($article['seo_description'] ?? '') ?></p>
        <div class="article-meta">
            <a href="<?= base_url('/blog?category=' . ($article['category_slug'] ?? '')) ?>" class="meta-link"><?= e($article['category_name'] ?? 'General') ?></a>
            <span><?= date('M d, Y', (int) $article['created_at']) ?></span>
            <span>Views: <?= (int) ($article['view_count'] ?? 0) ?></span>
            <span>By Finance Editor</span>
            <span><?= (int) ($article['like_count'] ?? 0) ?> likes</span>
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
            <div class="share-buttons">
                <a class="share-btn twitter" aria-label="Share on X" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.5 3h3.6l-7.9 9 9.3 9h-7.3l-5.6-6.9L3.5 21H0l8.4-9.6L-.6 3h7.5l5 6.2L17.5 3zm-1.3 16h2l-11.5-14h-2l11.5 14z"/></svg>
                </a>
                <a class="share-btn facebook" aria-label="Share on Facebook" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 8.5V6.8c0-.6.4-.8 1-.8h1.8V3h-2.5c-2.6 0-3.8 1.6-3.8 3.7v1.8H8v3h2V21h3.5v-9.5H16l.5-3h-3z"/></svg>
                </a>
                <a class="share-btn mail" aria-label="Share by email" href="mailto:?subject=<?= urlencode($article['title']) ?>&body=<?= urlencode(current_url()) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2zm0 2v.5l9 5.2 9-5.2V7H3zm18 10v-6.2l-9 5.2-9-5.2V17h18z"/></svg>
                </a>
                <form method="post" action="<?= base_url('/like') ?>" class="like-form">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                    <input type="hidden" name="article_id" value="<?= (int) $article['id'] ?>">
                    <input type="hidden" name="slug" value="<?= e($article['slug']) ?>">
                    <button class="share-btn like" type="submit" aria-label="Like this article">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-6.7-4.3-9.3-8C.7 9.8 2 6.5 5.3 5.7 7.4 5.2 9.4 6 10.5 7.5 11.6 6 13.6 5.2 15.7 5.7 19 6.5 20.3 9.8 21.3 13 18.7 16.7 12 21 12 21z"/></svg>
                    </button>
                </form>
            </div>
            <span class="like-count"><?= (int) ($article['like_count'] ?? 0) ?> likes</span>
            <a class="amp-link" href="<?= e($ampUrl) ?>">AMP Version</a>
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
