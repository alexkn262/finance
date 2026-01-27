<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>FIRE calculator</h1>
        <p>Estimate your financial independence target.</p>
        <div class="article-content">
            <p><strong>Formula:</strong> Target = Annual expenses / withdrawal rate</p>
            <p><strong>Where:</strong> Annual expenses = yearly spending, withdrawal rate = sustainable %.</p>
        </div>
    </div>
</section>
<section class="tool">
    <div class="container" data-stepper>
        <form class="tool-form" data-tool="fire" data-currency>
            <div class="tool-step active" data-step>
                <h3>Step 1: Expenses</h3>
                <label>Currency
                    <select name="currency">
                        <option value="$">$ USD</option>
                        <option value="€">€ EUR</option>
                        <option value="£">£ GBP</option>
                    </select>
                </label>
                <label>Annual expenses <input type="number" name="expenses" value="40000"></label>
                <div class="step-actions">
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <h3>Step 2: Withdrawal rate</h3>
                <label>Safe withdrawal rate (%) <input type="number" step="0.1" name="rate" value="4"></label>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <h3>Step 3: Review projection</h3>
                <p>We will estimate the portfolio size needed to sustain expenses.</p>
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
