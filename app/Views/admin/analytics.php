<?php ob_start(); ?>
<section class="admin-header">
    <h1>Analytics overview</h1>
</section>
<section class="admin-metrics">
    <div class="metric">
        <h3>Top pages (24h)</h3>
        <ul>
            <?php foreach ($pages as $page): ?>
                <li><?= e($page['page']) ?> — <?= (int) $page['views'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="metric">
        <h3>Top pages (all time)</h3>
        <ul>
            <?php foreach ($topAll as $page): ?>
                <li><?= e($page['page']) ?> — <?= (int) $page['views'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<section class="admin-chart">
    <h2>Unique visitors (7 days)</h2>
    <div class="chart bar-chart" id="analytics-chart">
        <ul class="bar-chart-list" id="analytics-bars"></ul>
    </div>
</section>
<script nonce="<?= e(App\Core\Security::cspNonce()) ?>">
    const renderBars = (trends) => {
        const values = trends.map((t) => t.views);
        const max = Math.max(...values, 1);
        const bars = document.getElementById('analytics-bars');
        if (!bars) return;
        bars.innerHTML = '';
        trends.forEach((trend) => {
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
    fetch('/admin/analytics/data')
        .then((res) => res.json())
        .then((data) => {
            if (!data.trends) return;
            renderBars(data.trends);
        });
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
<?php $content = ob_get_clean(); ?>
<?php view('admin/layout', ['content' => $content, 'title' => 'Analytics']); ?>
