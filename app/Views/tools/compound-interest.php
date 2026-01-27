<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Compound interest calculator</h1>
        <p>Estimate long-term growth of your investments.</p>
    </div>
</section>
<section class="tool">
    <div class="container">
        <div class="tool-steps">
            <div class="card">Step 1: Set your starting investment.</div>
            <div class="card">Step 2: Add monthly contributions.</div>
            <div class="card">Step 3: Test multiple rates and horizons.</div>
        </div>
        <form class="tool-form" data-tool="compound">
            <label>Initial investment <input type="number" name="principal" value="1000"></label>
            <label>Monthly contribution <input type="number" name="monthly" value="200"></label>
            <label>Annual return (%) <input type="number" step="0.1" name="rate" value="7"></label>
            <label>Years <input type="number" name="years" value="20"></label>
            <button class="btn" type="button" data-calc>Calculate</button>
        </form>
        <div class="tool-result" data-result></div>
        <div class="article-content">
            <p>Formula: FV = P(1+r/n)^{nt} + PMT((1+r/n)^{nt}-1)/(r/n)</p>
        </div>
        <p class="disclaimer">This calculator is for educational purposes only and not financial advice.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
