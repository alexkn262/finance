<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Real inflation calculator</h1>
        <p>See how purchasing power changes over time.</p>
    </div>
</section>
<section class="tool">
    <div class="container">
        <form class="tool-form" data-tool="inflation">
            <label>Current price <input type="number" name="amount" value="100"></label>
            <label>Inflation rate (%) <input type="number" step="0.1" name="rate" value="3"></label>
            <label>Years <input type="number" name="years" value="10"></label>
            <button class="btn" type="button" data-calc>Calculate</button>
        </form>
        <div class="tool-result" data-result></div>
        <p class="disclaimer">This calculator is for educational purposes only and not financial advice.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
