<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Real inflation calculator</h1>
        <p>See how purchasing power changes over time.</p>
        <div class="article-content">
            <p><strong>Formula:</strong> Future price = Current price × (1 + inflation rate)^years</p>
            <p><strong>Where:</strong> Current price = today's cost, inflation rate = annual %.</p>
        </div>
    </div>
</section>
<section class="tool">
    <div class="container" data-stepper>
        <form class="tool-form" data-tool="inflation" data-currency>
            <div class="tool-step active" data-step>
                <h3>Step 1: Current price</h3>
                <label>Currency
                    <select name="currency">
                        <option value="$">$ USD</option>
                        <option value="€">€ EUR</option>
                        <option value="£">£ GBP</option>
                    </select>
                </label>
                <label>Current price <input type="number" name="amount" value="100"></label>
                <div class="step-actions">
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <h3>Step 2: Inflation assumptions</h3>
                <label>Inflation rate (%) <input type="number" step="0.1" name="rate" value="3"></label>
                <label>Years <input type="number" name="years" value="10"></label>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <h3>Step 3: Review projection</h3>
                <p>We will estimate the inflated price for the time horizon.</p>
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
