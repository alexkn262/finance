<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Real inflation calculator</h1>
        <p>See how purchasing power changes over time.</p>
    </div>
</section>
<section class="tool">
    <div class="container" data-stepper>
        <div class="tool-steps">
            <div class="card">Step 1: Current price.</div>
            <div class="card">Step 2: Inflation assumptions.</div>
            <div class="card">Step 3: Review future cost.</div>
        </div>
        <form class="tool-form" data-tool="inflation">
            <div class="tool-step active" data-step>
                <label>Current price <input type="number" name="amount" value="100"></label>
                <div class="step-actions">
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <label>Inflation rate (%) <input type="number" step="0.1" name="rate" value="3"></label>
                <label>Years <input type="number" name="years" value="10"></label>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <p>Review your assumptions and generate the projection.</p>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-show-result>Show result</button>
                </div>
            </div>
            <button class="btn" type="button" data-calc hidden>Calculate</button>
        </form>
        <div class="tool-result" data-result></div>
        <div class="article-content">
            <p>Formula: Future price = Current price × (1 + inflation rate)^years</p>
        </div>
        <p class="disclaimer">This calculator is for educational purposes only and not financial advice.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
