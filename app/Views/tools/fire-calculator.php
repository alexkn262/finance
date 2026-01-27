<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>FIRE calculator</h1>
        <p>Estimate your financial independence target.</p>
    </div>
</section>
<section class="tool">
    <div class="container" data-stepper>
        <div class="tool-steps">
            <div class="card">Step 1: Annual expenses.</div>
            <div class="card">Step 2: Withdrawal rate.</div>
            <div class="card">Step 3: Review target.</div>
        </div>
        <form class="tool-form" data-tool="fire" data-currency>
            <div class="tool-step active" data-step>
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
                <label>Safe withdrawal rate (%) <input type="number" step="0.1" name="rate" value="4"></label>
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
            <p>Formula: Target = Annual expenses / withdrawal rate</p>
            <p>This estimates the portfolio size needed to sustain your expenses.</p>
        </div>
        <p class="disclaimer">This calculator is for educational purposes only and not financial advice.</p>
    </div>
</section>
<?php $content = ob_get_clean(); ?>
<?php view('layout', compact('content')); ?>
