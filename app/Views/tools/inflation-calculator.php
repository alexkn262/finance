<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Real inflation calculator</h1>
        <p>See how purchasing power changes over time.</p>
        <div class="formula-card bootstrap-card">
            <strong>Formula</strong>
            <div>Future price = Current price × (1 + inflation rate)^years</div>
            <div class="formula-grid">
                <div><span>Current price</span> Today's cost of the item.</div>
                <div><span>Rate</span> Annual inflation percentage.</div>
                <div><span>Years</span> Time horizon in years.</div>
            </div>
        </div>
    </div>
</section>
<section class="tool">
        <div class="container tool-stepper" data-stepper>
        <form class="tool-form" data-tool="inflation" data-currency>
            <div class="tool-step active step-page" data-step>
                <h3>Current price</h3>
                <p>Start with today's price of the item or expense.</p>
                <label>Currency
                    <select name="currency">
                        <option value="$">$ USD</option>
                        <option value="€">€ EUR</option>
                        <option value="£">£ GBP</option>
                    </select>
                </label>
                <label>Current price <input type="number" name="amount" value="100"></label>
                <span class="field-help">Current cost of the item or expense.</span>
                <div class="step-actions">
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step step-page" data-step>
                <h3>Inflation assumptions</h3>
                <p>Set the annual inflation rate and time horizon.</p>
                <label>Inflation rate (%) <input type="number" step="0.1" name="rate" value="3"></label>
                <span class="field-help">Expected yearly inflation.</span>
                <label>Years <input type="number" name="years" value="10"></label>
                <span class="field-help">How far in the future.</span>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step step-page" data-step>
                <h3>Review projection</h3>
                <p>Review and generate the future cost projection.</p>
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
