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

document.querySelectorAll('[data-calc]').forEach((button) => {
    button.addEventListener('click', () => {
        const form = button.closest('.tool-form');
        const result = form.parentElement.querySelector('[data-result]');
        const tool = form.dataset.tool;
        const data = Object.fromEntries(new FormData(form).entries());
        let output = '';
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
            output = `Projected balance: $${total.toFixed(2)}`;
        }
        if (tool === 'loan') {
            const amount = parseFloat(data.amount);
            const monthlyRate = parseFloat(data.rate) / 100 / 12;
            const months = parseFloat(data.years) * 12;
            const payment = (amount * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -months));
            output = `Monthly payment: $${payment.toFixed(2)}`;
        }
        if (tool === 'fire') {
            const expenses = parseFloat(data.expenses);
            const rate = parseFloat(data.rate) / 100;
            const target = expenses / rate;
            output = `FIRE number: $${target.toFixed(2)}`;
        }
        if (tool === 'inflation') {
            const amount = parseFloat(data.amount);
            const rate = parseFloat(data.rate) / 100;
            const years = parseFloat(data.years);
            const future = amount * Math.pow(1 + rate, years);
            output = `Future price: $${future.toFixed(2)}`;
        }
        result.textContent = output;
    });
});
