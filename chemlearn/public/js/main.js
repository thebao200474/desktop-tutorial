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
    initializeDecoration();
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

function initializeDecoration() {
    const toggleButtons = document.querySelectorAll('[data-decor-toggle]');
    const panel = document.querySelector('[data-decor-panel]');
    const layer = document.querySelector('[data-decor-layer]');
    const settingsPanel = document.querySelector('[data-settings-panel]');
    const settingsToggle = document.querySelector('[data-settings-toggle]');

    if (toggleButtons.length === 0 || !panel || !layer) {
        return;
    }

    toggleButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            const isHidden = panel.classList.contains('d-none');
            panel.classList.toggle('d-none');
            if (settingsPanel && settingsToggle) {
                settingsPanel.classList.add('d-none');
                settingsToggle.setAttribute('aria-expanded', 'false');
            }
            if (isHidden) {
                positionDecorPanel(panel);
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (!panel.contains(event.target) && !Array.from(toggleButtons).some((btn) => btn.contains(event.target))) {
            panel.classList.add('d-none');
        }
    });

    panel.querySelectorAll('[data-decor-icon]').forEach((iconButton) => {
        iconButton.addEventListener('click', () => {
            const symbol = iconButton.getAttribute('data-decor-icon');
            if (!symbol) {
                return;
            }
            addDecorIcon(symbol, layer);
        });
    });
}

function positionDecorPanel(panel) {
    const rect = panel.getBoundingClientRect();
    const viewportWidth = window.innerWidth;
    if (rect.right > viewportWidth) {
        panel.style.right = '1rem';
    } else {
        panel.style.right = '';
    }
}

function addDecorIcon(symbol, layer) {
    const icon = document.createElement('span');
    icon.className = 'decor-icon';
    icon.textContent = symbol;
    layer.appendChild(icon);

    const layerRect = layer.getBoundingClientRect();
    const initialLeft = clamp(layerRect.width / 2 - 20 + (Math.random() * 120 - 60), 0, layerRect.width - 40);
    const initialTop = clamp(layerRect.height / 2 - 20 + (Math.random() * 120 - 60), 0, layerRect.height - 40);
    icon.style.left = `${initialLeft}px`;
    icon.style.top = `${initialTop}px`;

    makeDraggable(icon, layerRect.width, layerRect.height);
}

function makeDraggable(element, maxWidth, maxHeight) {
    element.addEventListener('pointerdown', (event) => {
        event.preventDefault();
        const rect = element.getBoundingClientRect();
        const offsetX = event.clientX - rect.left;
        const offsetY = event.clientY - rect.top;

        const move = (moveEvent) => {
            const left = clamp(moveEvent.clientX - offsetX, 0, maxWidth - rect.width);
            const top = clamp(moveEvent.clientY - offsetY, 0, maxHeight - rect.height);
            element.style.left = `${left}px`;
            element.style.top = `${top}px`;
        };

        const up = () => {
            document.removeEventListener('pointermove', move);
            document.removeEventListener('pointerup', up);
        };

        document.addEventListener('pointermove', move);
        document.addEventListener('pointerup', up);
    });
}

function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
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
