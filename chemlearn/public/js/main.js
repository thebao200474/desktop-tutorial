document.addEventListener('DOMContentLoaded', () => {
    const scrollTopBtn = document.querySelector('[data-scroll-top]');
    if (!scrollTopBtn) {
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
});
