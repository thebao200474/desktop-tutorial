document.addEventListener('DOMContentLoaded', () => {
    const scrollTopBtn = document.querySelector('[data-scroll-top]');
    if (!scrollTopBtn) {
        initializeFloatingActions();
        return;
    }

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

    initializeFloatingActions();
});

function initializeFloatingActions() {
    const settingsContainer = document.querySelector('[data-settings]');
    const chatContainer = document.querySelector('[data-chat]');

    if (settingsContainer) {
        const toggle = settingsContainer.querySelector('[data-settings-toggle]');
        const panel = settingsContainer.querySelector('[data-settings-panel]');
        if (toggle && panel) {
            toggle.addEventListener('click', (event) => {
                event.stopPropagation();
                const isHidden = panel.classList.contains('d-none');
                panel.classList.toggle('d-none');
                toggle.setAttribute('aria-expanded', String(isHidden));
            });

            document.addEventListener('click', (event) => {
                if (!settingsContainer.contains(event.target)) {
                    panel.classList.add('d-none');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    }

    if (chatContainer) {
        const toggle = chatContainer.querySelector('[data-chat-toggle]');
        const panel = chatContainer.querySelector('[data-chat-panel]');
        if (toggle && panel) {
            toggle.addEventListener('click', (event) => {
                event.stopPropagation();
                const isHidden = panel.classList.contains('d-none');
                panel.classList.toggle('d-none');
                toggle.setAttribute('aria-expanded', String(isHidden));
            });

            document.addEventListener('click', (event) => {
                if (!chatContainer.contains(event.target)) {
                    panel.classList.add('d-none');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    }
}
