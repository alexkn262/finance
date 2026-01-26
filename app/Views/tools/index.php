<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Finance tools</h1>
        <p>Interactive calculators built for clarity and confidence.</p>
    </div>
</section>
<section class="tools-grid">
    <div class="container">
        <div class="grid">
            <a class="card" href="<?= base_url('/tools/compound-interest') ?>">Compound Interest Calculator</a>
            <a class="card" href="<?= base_url('/tools/loan-calculator') ?>">Loan Payment Calculator</a>
            <a class="card" href="<?= base_url('/tools/fire-calculator') ?>">FIRE Calculator</a>
            <a class="card" href="<?= base_url('/tools/inflation-calculator') ?>">Real Inflation Calculator</a>
        </div>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
