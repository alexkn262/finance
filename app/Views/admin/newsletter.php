<?php ob_start(); ?>
<section class="admin-header">
    <h1>Newsletter subscribers</h1>
    <div class="metric">Active subscribers <strong><?= (int) $count ?></strong></div>
</section>
<form method="post" action="<?= base_url('/admin/newsletter/send') ?>" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <label>Subject <input type="text" name="subject" required></label>
    <label>Body <textarea name="body" rows="6" required></textarea></label>
    <button class="btn" type="submit">Send newsletter</button>
</form>
<table class="admin-table">
    <thead>
        <tr><th>Email</th><th>Status</th><th>Joined</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($subscribers as $subscriber): ?>
            <tr>
                <td><?= e($subscriber['email']) ?></td>
                <td><?= e($subscriber['status']) ?></td>
                <td><?= date('Y-m-d', (int) $subscriber['created_at']) ?></td>
                <td>
                    <form method="post" action="<?= base_url('/admin/newsletter/delete') ?>">
                        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int) $subscriber['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Newsletter']); ?>
