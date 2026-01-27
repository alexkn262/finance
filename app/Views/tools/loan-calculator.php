<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Loan payment calculator</h1>
        <p>Understand monthly payments with clear assumptions.</p>
        <div class="article-content">
            <p><strong>Formula:</strong> Payment = P * r / (1 - (1 + r)^-n)</p>
            <p><strong>Where:</strong> P = loan amount, r = monthly interest rate, n = total months.</p>
        </div>
    </div>
</section>
<section class="tool">
    <div class="container" data-stepper>
        <form class="tool-form" data-tool="loan" data-currency>
            <div class="tool-step active" data-step>
                <h3>Step 1: Loan details</h3>
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
                <h3>Step 2: Interest + term</h3>
                <label>APR (%) <input type="number" step="0.1" name="rate" value="6"></label>
                <label>Years <input type="number" name="years" value="5"></label>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <h3>Step 3: Review projection</h3>
                <p>We will compute the monthly payment using your inputs.</p>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-show-result>Show result</button>
                </div>
            </div>
            <button class="btn" type="button" data-calc hidden>Calculate</button>
        </form>
        <div class="tool-result" data-result></div>
        <p class="disclaimer">This calculator is for educational purposes only and not financial advice.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
