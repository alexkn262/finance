<?php ob_start(); ?>
<section class="admin-header">
    <div>
        <h1>Dashboard</h1>
        <p>Overview of your finance platform performance.</p>
    </div>
    <div class="admin-actions">
        <a class="btn" href="<?= base_url('/admin/articles/create') ?>">New article</a>
    </div>
</section>
<section class="admin-metrics">
    <div class="metric"><h3>Articles</h3><strong><?= (int) $articles ?></strong></div>
    <div class="metric"><h3>Categories</h3><strong><?= (int) $categories ?></strong></div>
    <div class="metric"><h3>Comments</h3><strong><?= (int) $comments ?></strong></div>
    <div class="metric"><h3>Page views</h3><strong><?= (int) $page_views ?></strong></div>
    <div class="metric"><h3>Unique visitors</h3><strong><?= (int) $unique_visitors ?></strong></div>
</section>
<section class="admin-chart">
    <h2>Traffic trends</h2>
    <div class="chart bar-chart" id="dashboard-chart">
        <ul class="bar-chart-list" id="dashboard-bars"></ul>
    </div>
</section>
<script nonce="<?= e(App\Core\Security::cspNonce()) ?>">
    const renderBars = (data) => {
        const values = data.map((t) => t.views);
        const max = Math.max(...values, 1);
        const bars = document.getElementById('dashboard-bars');
        if (!bars) return;
        bars.innerHTML = '';
        data.forEach((trend) => {
            const li = document.createElement('li');
            const top = document.createElement('div');
            top.className = 'bar-top';
            const bottom = document.createElement('div');
            bottom.className = 'bar-bottom';
            bottom.style.height = `${Math.max((trend.views / max) * 160, 12)}px`;
            const label = document.createElement('span');
            const date = new Date(trend.day);
            label.textContent = date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit' });
            const value = document.createElement('strong');
            value.textContent = trend.views;
            bottom.appendChild(value);
            li.appendChild(top);
            li.appendChild(bottom);
            li.appendChild(label);
            bars.appendChild(li);
        });
    };
    const dashboardData = <?= json_encode($trends, JSON_UNESCAPED_SLASHES) ?>;
    renderBars(dashboardData);
    setInterval(() => {
        fetch('/admin/analytics/data')
            .then((res) => res.json())
            .then((data) => {
                if (data.trends) {
                    renderBars(data.trends);
                }
            })
            .catch(() => {});
    }, 30000);
</script>
<section class="admin-table-wrap">
    <h2>Recent articles</h2>
    <table class="admin-table">
        <thead>
            <tr><th>Title</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($recentArticles as $article): ?>
                <tr>
                    <td><?= e($article['title']) ?></td>
                    <td><?= e($article['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Dashboard']); ?>
