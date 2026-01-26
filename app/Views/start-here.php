<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Start here: build your financial journey.</h1>
        <p>Choose your path and follow a curated learning sequence.</p>
    </div>
</section>
<section class="paths">
    <div class="container">
        <div class="grid">
            <div class="card">
                <h3>Beginner</h3>
                <ul>
                    <li>Budget foundations</li>
                    <li>Debt payoff strategy</li>
                    <li>Emergency fund playbook</li>
                </ul>
            </div>
            <div class="card">
                <h3>Intermediate</h3>
                <ul>
                    <li>Index investing</li>
                    <li>Tax efficiency</li>
                    <li>Automated savings</li>
                </ul>
            </div>
            <div class="card">
                <h3>Advanced</h3>
                <ul>
                    <li>Portfolio optimization</li>
                    <li>Real estate analysis</li>
                    <li>Exit strategies</li>
                </ul>
            </div>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
