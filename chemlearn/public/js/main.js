document.addEventListener('DOMContentLoaded', () => {
    const scrollTopBtn = document.querySelector('[data-scroll-top]');
    if (scrollTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 200) {
                scrollTopBtn.classList.remove('d-none');
            } else {
                scrollTopBtn.classList.add('d-none');
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    initializeFloatingActions();
    initializePeriodicFilter();
});

function initializeFloatingActions() {
    setupFloatingContainer(document.querySelector('[data-settings]'), '[data-settings-toggle]', '[data-settings-panel]');
}

function setupFloatingContainer(container, toggleSelector, panelSelector) {
    if (!container) {
        return;
    }

    const toggle = container.querySelector(toggleSelector);
    const panel = container.querySelector(panelSelector);
    if (!toggle || !panel) {
        return;
    }

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();
        const isHidden = panel.classList.contains('d-none');
        panel.classList.toggle('d-none');
        toggle.setAttribute('aria-expanded', String(isHidden));
    });

    panel.addEventListener('click', (event) => {
        event.stopPropagation();
    });

    document.addEventListener('click', (event) => {
        if (!container.contains(event.target)) {
            panel.classList.add('d-none');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
}

function initializePeriodicFilter() {
    const filter = document.querySelector('[data-element-filter]');
    const grid = document.querySelector('[data-element-grid]');
    if (!filter || !grid) {
        return;
    }

    const cards = Array.from(grid.querySelectorAll('.periodic-card'));
    filter.addEventListener('input', () => {
        const query = filter.value.trim().toLowerCase();
        cards.forEach((card) => {
            const name = card.getAttribute('data-element-name') || '';
            const symbol = card.getAttribute('data-element-symbol') || '';
            const matches = query === '' || name.includes(query) || symbol.includes(query);
            card.style.display = matches ? '' : 'none';
        });
    });
}
