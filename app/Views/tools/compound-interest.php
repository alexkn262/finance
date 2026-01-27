<?php ob_start(); ?>
<section class="hero compact">
    <div class="container">
        <h1>Compound interest calculator</h1>
        <p>Project long-term growth with detailed assumptions.</p>
        <div class="article-content">
            <p><strong>Formula:</strong> FV = P(1+r/n)^{nt} + PMT((1+r/n)^{nt}-1)/(r/n)</p>
            <p><strong>Where:</strong> P = initial principal, r = annual rate, n = compounding periods/year, t = years.</p>
        </div>
    </div>
</section>
<section class="tool">
    <div class="container" data-stepper>
        <form class="tool-form" data-tool="compound" data-currency>
            <div class="tool-step active" data-step>
                <h3>Step 1: Starting details</h3>
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
                <h3>Step 2: Growth assumptions</h3>
                <label>Annual return (%) <input type="number" step="0.1" name="rate" value="7"></label>
                <label>Years <input type="number" name="years" value="20"></label>
                <div class="step-actions">
                    <button class="btn" type="button" data-prev>Back</button>
                    <button class="btn" type="button" data-next>Next</button>
                </div>
            </div>
            <div class="tool-step" data-step>
                <h3>Step 3: Review projection</h3>
                <p>We will combine your inputs to estimate the projected balance.</p>
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
