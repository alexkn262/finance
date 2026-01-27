<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>FIRE calculator</h1>
        <p>Estimate your financial independence target.</p>
        <div class="formula-card">
            <strong>Formula</strong>
            <div>Target = Annual expenses / withdrawal rate</div>
            <div class="formula-grid">
                <div><span>Expenses</span> yearly spending</div>
                <div><span>Rate</span> sustainable withdrawal %</div>
            </div>
        </div>
    </div>
</section>
<section class="tool">
    <div class="container" data-stepper>
        <form class="tool-form" data-tool="fire" data-currency>
            <div class="tool-step active" data-step>
                <h3>Step 1: Expenses</h3>
                <p>Tell us your annual spending baseline.</p>
                <label>Currency
                    <select name="currency">
                        <option value="$">$ USD</option>
                        <option value="€">€ EUR</option>
                        <option value="£">£ GBP</option>
                    </select>
                </label>
                <label>Annual expenses <input type="number" name="expenses" value="40000"></label>
                <span class="field-help">Your estimated yearly spending.</span>
                <div class="step-actions">
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <h3>Step 2: Withdrawal rate</h3>
                <p>Set the sustainable withdrawal rate you plan to use.</p>
                <label>Safe withdrawal rate (%) <input type="number" step="0.1" name="rate" value="4"></label>
                <span class="field-help">Typical range: 3%–5%.</span>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <h3>Step 3: Review projection</h3>
                <p>Review your plan and generate your FIRE target.</p>
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
