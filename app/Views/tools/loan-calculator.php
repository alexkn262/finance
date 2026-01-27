<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Loan payment calculator</h1>
        <p>Understand your monthly loan payment instantly.</p>
    </div>
</section>
<section class="tool">
    <div class="container" data-stepper>
        <div class="tool-steps">
            <div class="card">Step 1: Loan amount.</div>
            <div class="card">Step 2: Rate + term.</div>
            <div class="card">Step 3: Review monthly payment.</div>
        </div>
        <form class="tool-form" data-tool="loan" data-currency>
            <div class="tool-step active" data-step>
                <label>Currency
                    <select name="currency">
                        <option value="$">$ USD</option>
                        <option value="€">€ EUR</option>
                        <option value="£">£ GBP</option>
                    </select>
                </label>
                <label>Loan amount <input type="number" name="amount" value="25000"></label>
                <div class="step-actions">
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <label>APR (%) <input type="number" step="0.1" name="rate" value="6"></label>
                <label>Years <input type="number" name="years" value="5"></label>
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
            <p>Formula: Payment = P * r / (1 - (1 + r)^-n)</p>
            <p>The formula divides your balance into equal monthly payments based on the rate and term.</p>
        </div>
        <p class="disclaimer">This calculator is for educational purposes only and not financial advice.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
