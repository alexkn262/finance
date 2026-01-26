<?php ob_start(); ?>
<section class="admin-header">
    <h1>Comments</h1>
</section>
<table class="admin-table">
    <thead>
        <tr><th>Article</th><th>Comment</th><th>Status</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($comments as $comment): ?>
            <tr>
                <td><?= e($comment['article_title']) ?></td>
                <td><?= e($comment['content']) ?></td>
                <td><?= e($comment['status']) ?></td>
                <td>
                    <form method="post" action="<?= base_url('/admin/comments/update') ?>">
                        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int) $comment['id'] ?>">
                        <select name="status">
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                            <option value="pending">Pending</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Comments']); ?>
