<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Loan payment calculator</h1>
        <p>Understand monthly payments with clear assumptions.</p>
        <div class="formula-card bootstrap-card">
            <strong>Formula</strong>
            <div>Payment = P * r / (1 - (1 + r)^-n)</div>
            <div class="formula-grid">
                <div><span>P</span> Total loan principal.</div>
                <div><span>r</span> Monthly interest rate.</div>
                <div><span>n</span> Total number of payments.</div>
            </div>
        </div>
    </div>
</section>
<section class="tool">
        <div class="container tool-stepper" data-stepper>
        <form class="tool-form" data-tool="loan" data-currency>
            <div class="tool-step active step-page" data-step>
                <h3>Loan details</h3>
                <p>Capture the loan balance and currency.</p>
                <label>Currency
                    <select name="currency">
                        <option value="$">$ USD</option>
                        <option value="€">€ EUR</option>
                        <option value="£">£ GBP</option>
                    </select>
                </label>
                <label>Loan amount <input type="number" name="amount" value="25000"></label>
                <span class="field-help">Total amount borrowed from the lender.</span>
                <div class="step-actions">
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step step-page" data-step>
                <h3>Interest + term</h3>
                <p>Enter the annual interest rate and payoff timeline.</p>
                <label>APR (%) <input type="number" step="0.1" name="rate" value="6"></label>
                <span class="field-help">Annual percentage rate charged by the lender.</span>
                <label>Years <input type="number" name="years" value="5"></label>
                <span class="field-help">Total repayment duration.</span>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step step-page" data-step>
                <h3>Review projection</h3>
                <p>Review your inputs and generate the monthly payment.</p>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-show-result>Show result</button>
                </div>
            </div>
        </form>
        <div class="tool-result" data-result></div>
        <p class="disclaimer">This calculator is for educational purposes only and not financial advice.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
