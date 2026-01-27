<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Compound interest calculator</h1>
        <p>Estimate long-term growth of your investments.</p>
    </div>
</section>
<section class="tool">
    <div class="container" data-stepper>
        <div class="tool-steps">
            <div class="card">Step 1: Starting balance + contribution details.</div>
            <div class="card">Step 2: Growth assumptions.</div>
            <div class="card">Step 3: Review projection.</div>
        </div>
        <form class="tool-form" data-tool="compound" data-currency>
            <div class="tool-step active" data-step>
                <label>Currency
                    <select name="currency">
                        <option value="$">$ USD</option>
                        <option value="€">€ EUR</option>
                        <option value="£">£ GBP</option>
                    </select>
                </label>
                <label>Initial investment <input type="number" name="principal" value="1000"></label>
                <label>Monthly contribution <input type="number" name="monthly" value="200"></label>
                <div class="step-actions">
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <label>Annual return (%) <input type="number" step="0.1" name="rate" value="7"></label>
                <label>Years <input type="number" name="years" value="20"></label>
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
            <p>Formula: FV = P(1+r/n)^{nt} + PMT((1+r/n)^{nt}-1)/(r/n)</p>
            <p>This formula compounds your starting balance and adds monthly contributions each period.</p>
        </div>
        <p class="disclaimer">This calculator is for educational purposes only and not financial advice.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
