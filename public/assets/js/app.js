const debounce = (fn, delay = 300) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
};

const searchInput = document.getElementById('live-search');
const searchResults = document.getElementById('search-results');

if (searchInput && searchResults) {
    searchInput.addEventListener('input', debounce(async (event) => {
        const term = event.target.value.trim();
        if (!term) {
            searchResults.classList.remove('active');
            searchResults.innerHTML = '';
            return;
        }
        try {
            const response = await fetch(`/search?q=${encodeURIComponent(term)}`);
            const data = await response.json();
            searchResults.innerHTML = '';
            data.results.forEach((item) => {
                const link = document.createElement('a');
                link.href = `/blog/${encodeURIComponent(item.slug)}`;
                link.textContent = item.title;
                searchResults.appendChild(link);
            });
            searchResults.classList.add('active');
        } catch (error) {
            searchResults.classList.remove('active');
            searchResults.innerHTML = '';
        }
    }, 300));
}

const calculateTool = (form) => {
    const result = form.parentElement.querySelector('[data-result]');
    const tool = form.dataset.tool;
    const data = Object.fromEntries(new FormData(form).entries());
    let output = '';
    const currency = data.currency || '$';
    if (tool === 'compound') {
        const principal = parseFloat(data.principal);
        const monthly = parseFloat(data.monthly);
        const rate = parseFloat(data.rate) / 100 / 12;
        const years = parseFloat(data.years);
        const months = years * 12;
        let total = principal;
        for (let i = 0; i < months; i++) {
            total = total * (1 + rate) + monthly;
        }
        output = `Projected balance: ${currency}${total.toFixed(2)}`;
    }
    if (tool === 'loan') {
        const amount = parseFloat(data.amount);
        const monthlyRate = parseFloat(data.rate) / 100 / 12;
        const months = parseFloat(data.years) * 12;
        const payment = (amount * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -months));
        output = `Monthly payment: ${currency}${payment.toFixed(2)}`;
    }
    if (tool === 'fire') {
        const expenses = parseFloat(data.expenses);
        const rate = parseFloat(data.rate) / 100;
        const target = expenses / rate;
        output = `FIRE number: ${currency}${target.toFixed(2)}`;
    }
    if (tool === 'inflation') {
        const amount = parseFloat(data.amount);
        const rate = parseFloat(data.rate) / 100;
        const years = parseFloat(data.years);
        const future = amount * Math.pow(1 + rate, years);
        output = `Future price: ${currency}${future.toFixed(2)}`;
    }
    result.textContent = output;
};

document.querySelectorAll('[data-stepper]').forEach((stepper) => {
    const steps = stepper.querySelectorAll('[data-step]');
    const nextButtons = stepper.querySelectorAll('[data-next]');
    const prevButtons = stepper.querySelectorAll('[data-prev]');
    const resultButton = stepper.querySelector('[data-show-result]');
    let current = 0;
    const update = () => {
        steps.forEach((step, index) => {
            step.classList.toggle('active', index === current);
        });
    };
    const syncButtons = () => {
        nextButtons.forEach((button) => {
            button.style.display = current < steps.length - 1 ? 'inline-flex' : 'none';
        });
        if (resultButton) {
            resultButton.style.display = current === steps.length - 1 ? 'inline-flex' : 'none';
        }
    };
    const animateStep = (direction = 'next') => {
        steps.forEach((step) => step.classList.remove('slide-next', 'slide-prev'));
        const active = steps[current];
        if (active) {
            active.classList.add('active');
            active.classList.add(direction === 'next' ? 'slide-next' : 'slide-prev');
        }
    };
    nextButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (current < steps.length - 1) {
                current += 1;
                update();
                animateStep('next');
                syncButtons();
            }
        });
    });
    prevButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (current > 0) {
                current -= 1;
                update();
                animateStep('prev');
                syncButtons();
            }
        });
    });
    if (resultButton) {
        resultButton.addEventListener('click', () => {
            const form = stepper.querySelector('.tool-form');
            if (form) {
                calculateTool(form);
            }
        });
    }
    update();
    animateStep('next');
    syncButtons();
});
