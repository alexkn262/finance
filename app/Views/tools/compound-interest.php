<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Compound interest calculator</h1>
        <p>Project long-term growth with detailed assumptions.</p>
        <div class="formula-card">
            <strong>Formula</strong>
            <div>FV = P(1+r/n)^{nt} + PMT((1+r/n)^{nt}-1)/(r/n)</div>
            <div class="formula-grid">
                <div><span>P</span> initial principal</div>
                <div><span>r</span> annual rate</div>
                <div><span>n</span> compounding periods/year</div>
                <div><span>t</span> years</div>
            </div>
        </div>
    </div>
</section>
<section class="tool">
    <div class="container" data-stepper>
        <form class="tool-form" data-tool="compound" data-currency>
            <div class="tool-step active" data-step>
                <h3>Step 1: Starting details</h3>
                <p>Let's start with the basics of your contribution plan.</p>
                <label>Currency
                    <select name="currency">
                        <option value="$">$ USD</option>
                        <option value="€">€ EUR</option>
                        <option value="£">£ GBP</option>
                    </select>
                </label>
                <label>Initial investment <input type="number" name="principal" value="1000"></label>
                <span class="field-help">Starting balance before growth begins.</span>
                <label>Monthly contribution <input type="number" name="monthly" value="200"></label>
                <span class="field-help">Recurring amount added each month.</span>
                <div class="step-actions">
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <h3>Step 2: Growth assumptions</h3>
                <p>Define the rate and the time horizon for compounding.</p>
                <label>Annual return (%) <input type="number" step="0.1" name="rate" value="7"></label>
                <span class="field-help">Expected yearly growth rate.</span>
                <label>Years <input type="number" name="years" value="20"></label>
                <span class="field-help">How long you plan to invest.</span>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <h3>Step 3: Review projection</h3>
                <p>Review your assumptions and generate the projection.</p>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-show-result>Show result</button>
                </div>
            </div>
            <button class="btn" type="button" data-calc hidden></button>
        </form>
        <div class="tool-result" data-result></div>
        <p class="disclaimer">This calculator is for educational purposes only and not financial advice.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
