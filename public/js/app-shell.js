(function () {
    const STORAGE_KEY = 'retraiteflow-theme';
    const THEME_TOGGLE_SELECTOR = '[data-theme-toggle]';

    function getPreferredTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        const normalized = theme === 'dark' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', normalized);
        document.documentElement.style.colorScheme = normalized;

        const themeColorMeta = document.querySelector('meta[name="theme-color"]');
        if (themeColorMeta) {
            themeColorMeta.setAttribute('content', normalized === 'dark' ? '#0f172a' : '#2563eb');
        }

        document.querySelectorAll(THEME_TOGGLE_SELECTOR).forEach((button) => {
            const icon = button.querySelector('.theme-toggle-icon');
            const label = button.querySelector('.theme-toggle-label');
            if (icon) {
                icon.className = `theme-toggle-icon fas fa-${normalized === 'dark' ? 'moon' : 'sun'}`;
            }
            if (label) {
                label.textContent = normalized === 'dark' ? 'Sombre' : 'Clair';
            }
            button.setAttribute('aria-label', normalized === 'dark' ? 'Activer le thème clair' : 'Activer le thème sombre');
            button.classList.toggle('is-dark', normalized === 'dark');
        });

        localStorage.setItem(STORAGE_KEY, normalized);
    }

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
        applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
    }

    function initTheme() {
        const storedTheme = localStorage.getItem(STORAGE_KEY);
        const theme = storedTheme === 'dark' || storedTheme === 'light' ? storedTheme : getPreferredTheme();
        applyTheme(theme);
    }

    function initMenus() {
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenuDropdown = document.getElementById('userMenuDropdown');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenuPanel = document.getElementById('mobileMenuPanel');

        const closeUserMenu = () => {
            if (userMenuBtn) userMenuBtn.classList.remove('active');
            if (userMenuDropdown) userMenuDropdown.classList.remove('show');
        };

        const closeMobileMenu = () => {
            if (mobileMenuToggle) mobileMenuToggle.classList.remove('active');
            if (mobileMenuPanel) mobileMenuPanel.classList.remove('show');
        };

        if (userMenuBtn && userMenuDropdown) {
            userMenuBtn.addEventListener('click', (event) => {
                event.stopPropagation();
                const isOpen = userMenuDropdown.classList.toggle('show');
                userMenuBtn.classList.toggle('active', isOpen);
            });
        }

        if (mobileMenuToggle && mobileMenuPanel) {
            mobileMenuToggle.addEventListener('click', (event) => {
                event.stopPropagation();
                const isOpen = mobileMenuPanel.classList.toggle('show');
                mobileMenuToggle.classList.toggle('active', isOpen);
            });
        }

        document.addEventListener('click', (event) => {
            if (userMenuBtn && userMenuDropdown && !userMenuBtn.contains(event.target) && !userMenuDropdown.contains(event.target)) {
                closeUserMenu();
            }
            if (mobileMenuToggle && mobileMenuPanel && !mobileMenuToggle.contains(event.target) && !mobileMenuPanel.contains(event.target)) {
                closeMobileMenu();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeUserMenu();
                closeMobileMenu();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initTheme();
        initMenus();

        document.querySelectorAll(THEME_TOGGLE_SELECTOR).forEach((button) => {
            button.addEventListener('click', toggleTheme);
        });

        document.querySelectorAll('.stat-card, .participants-section, .chart-panel, .rf-card, .form-container, .modal, .search-bar-small').forEach((element, index) => {
            element.style.transitionDelay = `${Math.min(index * 30, 150)}ms`;
            element.classList.add('is-ready');
        });
    });
})();
