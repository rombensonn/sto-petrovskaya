(() => {
    const desktopMotion = window.matchMedia('(min-width: 781px) and (hover: hover) and (prefers-reduced-motion: no-preference)');

    const phoneInput = document.querySelector('input[name="phone"]');
    const staticPagesMode = document.documentElement.dataset.static === 'github-pages';

    if (phoneInput) {
        phoneInput.addEventListener('input', () => {
            phoneInput.value = phoneInput.value.replace(/[^\d+\-()\s]/g, '').slice(0, 32);
        });
    }

    const form = document.querySelector('.lead-form');
    if (form) {
        if (staticPagesMode) {
            const notice = document.createElement('div');
            notice.className = 'notice notice--static';
            notice.setAttribute('role', 'status');
            notice.textContent = 'На GitHub Pages форма работает как демонстрация. Для записи позвоните напрямую: +7 (977) 385-05-72.';
            form.prepend(notice);

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                notice.textContent = 'Для быстрой записи позвоните: +7 (977) 385-05-72. PHP-обработка формы доступна при размещении на PHP-хостинге.';
                notice.scrollIntoView({ block: 'nearest' });
            });
        } else {
            form.addEventListener('submit', () => {
                const button = form.querySelector('button[type="submit"]');
                if (button) {
                    button.textContent = 'Отправляем...';
                    button.disabled = true;
                }
            });
        }
    }

    const currentHashTarget = () => document.querySelector(window.location.hash);
    if (window.location.hash && currentHashTarget()) {
        window.setTimeout(() => currentHashTarget().scrollIntoView({ block: 'start' }), 80);
    }

    document.body.classList.add('cta-script-ready');
    const syncMobileCta = () => {
        const deepAnchor = window.location.hash && window.location.hash !== '#top';
        document.body.classList.toggle('show-mobile-cta', window.scrollY > 360 || Boolean(deepAnchor));
    };
    syncMobileCta();
    window.addEventListener('scroll', syncMobileCta, { passive: true });
    window.addEventListener('hashchange', syncMobileCta);

    if (desktopMotion.matches && 'IntersectionObserver' in window) {
        document.body.classList.add('reveal-ready');
        const revealTargets = document.querySelectorAll('[data-reveal], .section-head, .service-card, .pain-grid article, .steps li, .review-card, .price-panel, .faq-list details');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.16, rootMargin: '0px 0px -40px 0px' });

        revealTargets.forEach((target) => {
            target.setAttribute('data-reveal', '');
            observer.observe(target);
        });
    }
})();
