// global.js
// ── Ionicons Injection ──────────────────────────────────────
document.addEventListener("DOMContentLoaded", () => {
    // Dynamically inject Ionicons to avoid repeating script tags in every HTML file
    if (!document.querySelector('script[src*="ionicons"]')) {
        const scriptModule = document.createElement('script');
        scriptModule.type = 'module';
        scriptModule.src = 'https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js';
        document.body.appendChild(scriptModule);

        const scriptNoModule = document.createElement('script');
        scriptNoModule.setAttribute('nomodule', '');
        scriptNoModule.src = 'https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js';
        document.body.appendChild(scriptNoModule);
    }

    // ── Navigation Toggle ───────────────────────────────────
    const navToggle = document.querySelector('.site-nav__toggle');
    const navMobile = document.querySelector('.site-nav__mobile');
    const navIcon = navToggle ? navToggle.querySelector('i') : null;

    if (navToggle && navMobile) {
        navToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = navMobile.classList.toggle('is-open');
            if (navIcon) {
                navIcon.className = isOpen ? 'fas fa-times' : 'fas fa-bars';
            }
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!navMobile.contains(e.target) && !navToggle.contains(e.target)) {
                navMobile.classList.remove('is-open');
                if (navIcon) navIcon.className = 'fas fa-bars';
            }
        });

        // Close when a mobile nav link is clicked
        navMobile.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navMobile.classList.remove('is-open');
                if (navIcon) navIcon.className = 'fas fa-bars';
            });
        });
    }
});
