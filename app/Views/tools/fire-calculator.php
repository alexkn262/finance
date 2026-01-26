<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>FIRE calculator</h1>
        <p>Estimate your financial independence target.</p>
    </div>
</section>
<section class="tool">
    <div class="container">
        <form class="tool-form" data-tool="fire">
            <label>Annual expenses <input type="number" name="expenses" value="40000"></label>
            <label>Safe withdrawal rate (%) <input type="number" step="0.1" name="rate" value="4"></label>
            <button class="btn" type="button" data-calc>Calculate</button>
        </form>
        <div class="tool-result" data-result></div>
        <div class="article-content">
            <p>Formula: Target = Annual expenses / withdrawal rate</p>
        </div>
        <p class="disclaimer">This calculator is for educational purposes only and not financial advice.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
