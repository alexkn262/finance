<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Loan payment calculator</h1>
        <p>Understand your monthly loan payment instantly.</p>
    </div>
</section>
<section class="tool">
    <div class="container">
        <div class="tool-steps">
            <div class="card">Step 1: Enter the loan balance.</div>
            <div class="card">Step 2: Adjust APR and duration.</div>
            <div class="card">Step 3: Compare scenarios.</div>
        </div>
        <form class="tool-form" data-tool="loan">
            <label>Loan amount <input type="number" name="amount" value="25000"></label>
            <label>APR (%) <input type="number" step="0.1" name="rate" value="6"></label>
            <label>Years <input type="number" name="years" value="5"></label>
            <button class="btn" type="button" data-calc>Calculate</button>
        </form>
        <div class="tool-result" data-result></div>
        <div class="article-content">
            <p>Formula: Payment = P * r / (1 - (1 + r)^-n)</p>
        </div>
        <p class="disclaimer">This calculator is for educational purposes only and not financial advice.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
